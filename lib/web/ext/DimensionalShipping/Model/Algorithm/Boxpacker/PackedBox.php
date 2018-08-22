<?php
/**
 * Box packing (3D bin packing, knapsack problem)
 *
 * @package BoxPacker
 * @author  Doug Wright
 */

namespace Aitoc\DimensionalShipping\Model\Algorithm\Boxpacker;

/**
 * A "box" with items
 *
 * @author  Doug Wright
 * @package BoxPacker
 */
class PackedBox
{

    /**
     * Box used
     *
     * @var Box
     */
    protected $box;

    /**
     * Items in the box
     *
     * @var ItemList
     */
    protected $items;

    /**
     * Total weight of box
     *
     * @var int
     */
    protected $weight;

    /**
     * Remaining width inside box for another item
     *
     * @var int
     */
    protected $remainingWidth;

    /**
     * Remaining length inside box for another item
     *
     * @var int
     */
    protected $remainingLength;

    /**
     * Remaining depth inside box for another item
     *
     * @var int
     */
    protected $remainingDepth;

    /**
     * Remaining weight inside box for another item
     *
     * @var int
     */
    protected $remainingWeight;

    /**
     * Used width inside box for packing items
     *
     * @var int
     */
    protected $usedWidth;

    /**
     * Used length inside box for packing items
     *
     * @var int
     */
    protected $usedLength;

    /**
     * Used depth inside box for packing items
     *
     * @var int
     */
    protected $usedDepth;

    /**
     * Constructor
     *
     * @param Box      $box
     * @param ItemList $itemList
     * @param int      $remainingWidth
     * @param int      $remainingLength
     * @param int      $remainingDepth
     * @param int      $remainingWeight
     * @param int      $usedWidth
     * @param int      $usedLength
     * @param int      $usedDepth
     */
    public function __construct(
        BoxInterface $box,
        ItemList $itemList,
        $remainingWidth,
        $remainingLength,
        $remainingDepth,
        $remainingWeight,
        $usedWidth,
        $usedLength,
        $usedDepth
    ) {
        $this->box             = $box;
        $this->items           = $itemList;
        $this->remainingWidth  = $remainingWidth;
        $this->remainingLength = $remainingLength;
        $this->remainingDepth  = $remainingDepth;
        $this->remainingWeight = $remainingWeight;
        $this->usedWidth       = $usedWidth;
        $this->usedLength      = $usedLength;
        $this->usedDepth       = $usedDepth;
    }

    /**
     * Get box used
     *
     * @return Box
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Get items packed
     *
     * @return ItemList
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get packed weight
     *
     * @return int weight in grams
     */
    public function getWeight()
    {

        if (!is_null($this->weight)) {
            return $this->weight;
        }

        $this->weight = $this->box->getEmptyWeight();
        $items        = clone $this->items;
        foreach ($items as $item) {
            $this->weight += $item->getWeight();
        }

        return $this->weight;
    }

    /**
     * Get remaining width inside box for another item
     *
     * @return int
     */
    public function getRemainingWidth()
    {
        return $this->remainingWidth;
    }

    /**
     * Get remaining length inside box for another item
     *
     * @return int
     */
    public function getRemainingLength()
    {
        return $this->remainingLength;
    }

    /**
     * Get remaining depth inside box for another item
     *
     * @return int
     */
    public function getRemainingDepth()
    {
        return $this->remainingDepth;
    }

    /**
     * Used width inside box for packing items
     *
     * @return int
     */
    public function getUsedWidth()
    {
        return $this->usedWidth;
    }

    /**
     * Used length inside box for packing items
     *
     * @return int
     */
    public function getUsedLength()
    {
        return $this->usedLength;
    }

    /**
     * Used depth inside box for packing items
     *
     * @return int
     */
    public function getUsedDepth()
    {
        return $this->usedDepth;
    }

    /**
     * Get remaining weight inside box for another item
     *
     * @return int
     */
    public function getRemainingWeight()
    {
        return $this->remainingWeight;
    }

    /**
     * Get volume utilisation of the packed box
     *
     * @return float
     */
    public function getVolumeUtilisation()
    {
        $itemVolume = 0;

        /** @var Item $item */
        foreach (clone $this->items as $item) {
            $itemVolume += $item->getVolume();
        }

        return round($itemVolume / $this->box->getInnerVolume() * 100, 1);
    }
}
