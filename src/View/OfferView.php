<?php

declare(strict_types=1);

namespace App\v1\View;

use App\Entity\Offer;

/**
 * Class OfferView.
 */
class OfferView
{
    /**
     * @param mixed $data
     */
    public static function render($data): array
    {
        $result = [];
        if ($data instanceof Offer) {
            $result = self::renderOne($data);
        } else {
            foreach ($data as $row) {
                if ($row instanceof Offer) {
                    $result[] = self::renderOne($row);
                }
            }
        }

        return $result;
    }

    public static function renderOne(Offer $offer): array
    {
        return $offer->jsonSerialize();
    }
}
