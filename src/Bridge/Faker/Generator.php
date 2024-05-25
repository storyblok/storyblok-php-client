<?php

declare(strict_types=1);

/**
 * This file is part of Storyblok-Api.
 *
 * (c) SensioLabs Deutschland <info@sensiolabs.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\Storyblok\Api\Bridge\Faker;

use Faker\Factory;
use Faker\Generator as BaseGenerator;
use SensioLabs\Storyblok\Api\Bridge\Faker\Provider\StoryblokProvider;

/**
 * @method array datasourceEntryResponse(array $overrides = [])
 * @method array datasourceResponse(array $overrides = [])
 * @method array linkAlternateResponse(array $overrides = [])
 * @method array linkResponse(array $overrides = [])
 * @method array linksResponse(array $overrides = [])
 * @method array storiesResponse(array $overrides = [])
 * @method array storyResponse(array $overrides = [])
 * @method array tagsResponse(array $overrides = [])
 */
final class Generator extends BaseGenerator
{
    public function __construct()
    {
        parent::__construct();

        // Get a default generator with default providers
        $generator = Factory::create('de_DE');

        $generator->seed(9001);

        // Add custom providers
        $generator->addProvider(new StoryblokProvider($generator));

        // copy default and custom providers to this custom generator
        foreach ($generator->getProviders() as $provider) {
            $this->addProvider($provider);
        }
    }
}
