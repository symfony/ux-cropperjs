<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\UX\Cropperjs\Tests\Kernel;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\UX\Cropperjs\CropperjsBundle;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 *
 * @internal
 */
class TwigAppKernel extends Kernel
{
    use AppKernelTrait;

    public function registerBundles(): iterable
    {
        return [new FrameworkBundle(), new TwigBundle(), new CropperjsBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $frameworkConfig = [
                'secret' => '$ecret',
                'test' => true,
                'http_method_override' => false,
                'php_errors' => ['log' => true],
                'validation' => [
                    'email_validation_mode' => 'html5',
                ],
            ];

            if (self::VERSION_ID >= 60200) {
                $frameworkConfig['handle_all_throwables'] = true;
            }

            $container->loadFromExtension('framework', $frameworkConfig);
            $container->loadFromExtension('twig', ['default_path' => __DIR__.'/templates', 'strict_variables' => true, 'exception_controller' => null]);

            // create a public alias - FormFactoryInterface is removed otherwise
            $container->setAlias('public_form_factory', new Alias(FormFactoryInterface::class, true));
        });
    }
}
