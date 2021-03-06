<?php

namespace ConnectFour\Game;

use ConnectFour\Game\Exception\EndGameException;
use ConnectFour\Game\Grid\Column;
use ConnectFour\Game\Grid\Helper;
use ConnectFour\Game\Grid\Validator;
use ConnectFour\Player\PlayerInterface;

/**
 * This class represents game state
 */
class Grid implements \JsonSerializable
{
    /**
     * Disk labels
     */
    const DISK_PLAYER_1 = 'd1';
    const DISK_PLAYER_2 = 'd2';

    /**
     * Grid size
     */
    const COUNT_COLUMN = 7;
    const COUNT_ROW = 6;

    /**
     * @var Column[]
     */
    private $columns;

    /**
     * @param null|array $grid array representation of grid
     */
    public function __construct($grid = null)
    {
        $this->columns = [];
        for ($i = 0; $i < self::COUNT_COLUMN; $i++) {
            $this->columns[] = new Column(($grid ? Helper::getColumn($grid, $i) : []), self::COUNT_ROW);
        }
    }

    /**
     * Adds disk to grid
     *
     * @param string $disk Grid::DISK_PLAYER_1 or Grid::DISK_PLAYER_2
     * @param int $column Index of column
     * @throws EndGameException
     */
    public function addDisk($disk, $column)
    {
        $column = (int) $column;
        if (isset($this->columns[$column])) {
            $this->columns[$column]->push($disk);
        } else {
            throw new \InvalidArgumentException(
                sprintf('Invalid column %d (%d columns grid)', $column, self::COUNT_COLUMN)
            );
        }
        Validator::validate($this);
    }

    /**
     * Returns array representation of grid
     *
     * @param null|string $disk Set to Grid::DISK_PLAYER_1 or Grid::DISK_PLAYER_2 to make representation for player
     * @return array
     */
    public function getRepresentation($disk = null)
    {
        $rows = [];

        $callback = null;
        if ($disk) {
            $callback = function ($value) use ($disk) {
                if ($value) {
                    return $disk == $value ? PlayerInterface::DISK_LABEL_MINE : PlayerInterface::DISK_LABEL_OPPONENT;
                }
                return 0;
            };
        }

        for ($i = 0; $i < self::COUNT_ROW; $i++) {
            $row = [];

            foreach ($this->columns as $column) {
                $row[] = $column->get($i);
            }

            $rows[] = $callback ? array_map($callback, $row) : $row;
        }

        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->getRepresentation();
    }

    /**
     * @return int Maximum moves count
     */
    public function getMaxMoves()
    {
        return self::COUNT_ROW * self::COUNT_COLUMN;
    }
}
