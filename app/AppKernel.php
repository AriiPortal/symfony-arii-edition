<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
        new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
        new Symfony\Bundle\SecurityBundle\SecurityBundle(),
        new Symfony\Bundle\TwigBundle\TwigBundle(),
        new Symfony\Bundle\MonologBundle\MonologBundle(),
        new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
        new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
        new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        new FOS\UserBundle\FOSUserBundle(),
        new FOS\RestBundle\FOSRestBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle(),
        new Arii\UserBundle\AriiUserBundle(),
        new Arii\CoreBundle\AriiCoreBundle(),
        new Arii\AdminBundle\AriiAdminBundle(),
        new Arii\JIDBundle\AriiJIDBundle(),
        new Arii\DSBundle\AriiDSBundle(),
        new Arii\I5Bundle\AriiI5Bundle(),
        new Arii\JOCBundle\AriiJOCBundle(),
        new Arii\MFTBundle\AriiMFTBundle(),
        new Arii\GraphvizBundle\AriiGraphvizBundle(),
        new Arii\ATSBundle\AriiATSBundle(),
        new Arii\ReportBundle\AriiReportBundle(),
        new Arii\TimeBundle\AriiTimeBundle(),
        new Arii\SelfBundle\AriiSelfBundle(),
        new Arii\JOBBundle\AriiJOBBundle(),
        new Arii\ACKBundle\AriiACKBundle(),
        new Arii\PlumbBundle\AriiPlumbBundle(),
        new Arii\BlocklyBundle\AriiBlocklyBundle()
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new TimeInc\SwaggerBundle\SwaggerBundle();
//            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
