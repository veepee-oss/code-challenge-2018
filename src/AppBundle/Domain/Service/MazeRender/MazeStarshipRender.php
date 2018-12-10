<?php

namespace AppBundle\Domain\Service\MazeRender;

/**
 * Class MazeStarshipRender
 *
 * @package AppBundle\Domain\Service\MazeRender
 */
class MazeStarshipRender extends MazeIconRender
{
    protected function getPlayedKilledCss($index, $direction)
    {
        return 'x-starship-explosion';
    }

    protected function getGhostNeutralCss($index, $direction, $display)
    {
        return 'x-ghost' . $display . '-neutral';
    }

    protected function getGhostCss($index, $direction, $display)
    {
        return 'x-ghost' . $display . '-regular';
    }

    protected function getGhostAngryCss($index, $direction, $display)
    {
        return 'x-ghost' . $display . '-angry';
    }

    protected function getGhostKilledCss($index, $direction, $display)
    {
        return 'x-ghost-explosion';
    }

    protected function getShotDirCss($direction)
    {
        return 'x-starship-shot-' . $direction;
    }
}
