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

    protected function getGhostNeutralCss($index, $direction)
    {
        $num = 1 + ($index % 4);
        return 'x-ghost' . $num . '-neutral';
    }

    protected function getGhostCss($index, $direction)
    {
        $num = 1 + ($index % 4);
        return 'x-ghost' . $num . '-regular';
    }

    protected function getGhostAngryCss($index, $direction)
    {
        $num = 1 + ($index % 4);
        return 'x-ghost' . $num . '-angry';
    }

    protected function getShotDirCss($direction)
    {
        return 'x-starship-shot-' . $direction;
    }
}
