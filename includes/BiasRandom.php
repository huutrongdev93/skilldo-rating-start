<?php
class BiasRandom
{
    /**
     * The data that will be randomed
     * Data structure [name_1 => weight_1, name_2 => weight_2].
     *
     * @var array
     */
    protected $data = [];

    /**
     * Constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data = $data;
    }

    /**
     * Add an element to $data.
     *
     * @param string|int $name
     * @param int $weight
     *
     * @return bool
     */
    public function addElement($name, $weight)
    {
        if ((is_string($name) || is_numeric($name)) && is_numeric($weight)) {
            $this->data[$name] = $weight;
            return true;
        }

        return false;
    }

    /**
     * Remove an item from $data.
     *
     * @param string|int $name
     */
    public function removeElement($name)
    {
        unset($this->data[$name]);
    }

    /**
     * Set $data.
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Get current $data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Random with bias from data array.
     *
     * @param array $data
     *
     * @return int|string
     */
    private function getRandom($data)
    {
        $total = 0;
        $distribution = [];
        foreach ($data as $name => $weight) {
            $total += $weight;
            $distribution[$name] = $total;
        }
        $rand = mt_rand(0, $total - 1);
        foreach ($distribution as $name => $weight) {
            if ($rand < $weight) {
                return $name;
            }
        }
    }

    /**
     * Get random data.
     *
     * @param int $count
     *
     * @return array
     */
    public function random($count = 1)
    {
        $data = $this->data;
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            if (!$data) {
                break;
            }
            $name = $this->getRandom($data);
            $result[] = $name;
            unset($data[$name]);
        }

        return $result;
    }
}