<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\AbstractAutoTests;

class CertificateHttps extends AbstractAutoTests
{
    public function collectData(): void
    {
        $url = $this->protocol . '://' . $this->domain;
        //$url = "https://gigant.ru";
        $context = stream_context_create([
            'ssl' => ['capture_peer_cert' => true],
        ]);
        $contextOpen = fopen($url, 'rb', false, $context);

        if (empty($contextOpen)) {
            return;
        }

        $params = stream_context_get_params($contextOpen);
        $this->data['certificate'] = openssl_x509_parse($params['options']['ssl']['peer_certificate']);
    }

    public function compare(): void
    {
        if (empty($this->data['certificate'])) {
            $this->result['errors'][] = 'Сертификат отсутствует.';
            parent::compare();
            return;
        }

        $certificate = $this->data['certificate'];
        $date = new \DateTimeImmutable();
        $dateTo = $date->setTimestamp((int)$certificate['validTo_time_t']);

        if ($certificate['validTo_time_t'] < $date->getTimestamp()) {
            $this->result['error'][] = 'Срок действия сертификата истек.';
            parent::compare();
            return;
        }

        if ($certificate['validTo_time_t'] - $date->getTimestamp() < (30 * 24 * 60 * 60)) {
            $this->result['message'][] = 'Сертификат активен.
            Действителен менее месяца. Срок действия заканчивается ' . $dateTo->format(
                    'd.m.Y'
                ) . '
            ';
        }

        parent::compare();
    }
}
