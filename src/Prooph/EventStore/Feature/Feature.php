<?php
/*
 * This file is part of the prooph/event-store.
 * (c) Alexander Miertsch <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * 
 * Date: 20.04.14 - 23:57
 */

namespace Prooph\EventStore\Feature;

use Prooph\EventStore\EventStore;

/**
 * Class FeatureInterface
 *
 * @package Prooph\EventStore\Feature
 * @author Alexander Miertsch <kontakt@codeliner.ws>
 */
interface Feature
{
    /**
     * @param EventStore $eventStore
     * @return void
     */
    public function setUp(EventStore $eventStore);
}
 