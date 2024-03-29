<?php

namespace App\Service;

use App\Entity\Cryptocurrency;
use App\Repository\CryptocurrencyRepository;
use Codenixsv\CoinGeckoApi\CoinGeckoClient;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class CryptocurrencyService
{
    /**
     * @param CoinGeckoClient $client
     * @param CryptocurrencyRepository $repoCryptocurrency
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly CoinGeckoClient $client,
        private readonly CryptocurrencyRepository $repoCryptocurrency,
        private readonly EntityManagerInterface $entityManager,
        private readonly LoggerInterface $logger
    )
    {
    }

    public function updateAllCryptos(): void
    {
        $cryptos = $this->repoCryptocurrency->findAll();
        foreach ($cryptos as $cryptocurrency) {
            $this->updateDatas($cryptocurrency);
        }
    }

    /**
     * Mets à jour toutes les infos de $cryptocurrency
     * @param Cryptocurrency $cryptocurrency
     */
    public function updateDatas(Cryptocurrency $cryptocurrency): void
    {
        try {
            $datas = $this->client->coins()->getCoin($cryptocurrency->getLibelleCoingecko());
            if (array_key_exists('market_data', $datas)) {
                //Mets à jour les prix
                if (array_key_exists('current_price', $datas['market_data']) && array_key_exists('usd', $datas['market_data']['current_price'])) {
                    $cryptocurrency->setPriceUsd($datas['market_data']['current_price']['usd']);
                }

                //Mets à jour le libelle
                if (array_key_exists('name', $datas)) {
                    $cryptocurrency->setLibelle($datas['name']);
                }

                //Mets à jour le symbol
                if (array_key_exists('symbol', $datas)) {
                    $cryptocurrency->setSymbol($datas['symbol']);
                }

                //Mets à jour les images
                if (array_key_exists('image', $datas)) {
                    if (array_key_exists('thumb', $datas['image'])) {
                        $cryptocurrency->setUrlImgThumb($datas['image']['thumb']);
                    }
                }

                //Mets à jour les market cap
                if (array_key_exists('market_cap', $datas['market_data']) && array_key_exists('usd', $datas['market_data']['market_cap'])) {
                    $cryptocurrency->setMcapUsd($datas['market_data']['market_cap']['usd']);
                }
            }
        } catch (Exception $e) {
            $this->logger->critical('Erreur lors de la récupération des données pour le coin ' . $cryptocurrency->getLibelleCoingecko() . '. Exception : ' . $e->getMessage());
        }
    }

    /**
     * Mets à jour seulement les prix de tout le catalogue crypto.
     */
    public function updatePrices(): void
    {
        $cryptos = $this->repoCryptocurrency->findAll();
        $libelles = array_map(static function ($crypto) {
            return $crypto->getLibelleCoingecko();
        }, $cryptos);
        $string = implode(',', $libelles);
        try {
            $prices = $this->client->simple()->getPrice($string, 'usd');
            foreach ($cryptos as $crypto) {
                $libelleCg = $crypto->getLibelleCoingecko();
                if (array_key_exists($libelleCg, $prices)) {
                    if (array_key_exists('usd', $prices[$libelleCg])) {
                        $crypto->setPriceUsd($prices[$libelleCg]['usd']);
                    } else {
                        $this->logger->warning('Erreur lors de la récupération des prix EN DOLLAR pour ' . $crypto->getLibelleCoingecko());
                    }
                    $this->entityManager->persist($crypto);
                } else {
                    $this->logger->warning('Erreur lors de la récupération des prix pour ' . $crypto->getLibelleCoingecko());
                }
            }
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->logger->critical('Erreur lors de la récupération des prix pour ' . $string . '. Exception : ' . $e->getMessage());
        }
    }
}