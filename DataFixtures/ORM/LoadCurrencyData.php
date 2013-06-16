<?php

namespace Pim\Bundle\DemoBundle\DataFixtures\ORM;

use Symfony\Component\Yaml\Yaml;
use Pim\Bundle\ConfigBundle\Entity\Currency;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load fixtures for currencies
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2012 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 */
class LoadCurrencyData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @staticvar multitype
     */
    protected static $activatedCurrencies = array('EUR', 'USD', 'GBP');

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $configCurrencies = $this->container->getParameter('pim_config.currencies');

        foreach ($configCurrencies['currencies'] as $currencyCode => $currencyName) {
            $activated = in_array($currencyCode, self::$activatedCurrencies);
            $currency = $this->createCurrency($currencyCode, $activated);
            $manager->persist($currency);
        }

        $manager->flush();
    }

    /**
     * Create currency entity and persist it
     * @param string  $code      Currency code
     * @param boolean $activated Define if currency is activated or not
     *
     * @return \Pim\Bundle\ConfigBundle\Entity\Currency
     */
    protected function createCurrency($code, $activated = false)
    {
        $currency = new Currency();
        $currency->setCode($code);
        $currency->setActivated($activated);

        $this->setReference('currency.'. $code, $currency);

        return $currency;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 0;
    }
}
