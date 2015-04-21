<?php

namespace Autopilot\Helper;

class Filters extends \Lime\Helper {

    protected $filters = [];

    public function initialize(){

    }

    public function add($name, $callback, $priority = 0){

        if (!isset($this->filters[$name])) $this->filters[$name] = [];

        // make $this available in closures
        if (is_object($callback) && $callback instanceof \Closure) {
            $callback = $callback->bindTo($this, $this);
        }

        $this->filters[$name][] = ["fn" => $callback, "prio" => $priority];

        return $this;
    }

    public function apply($filter, $value, $args = []){

        if (!isset($this->filters[$filter])){
            return $value;
        }

        if (!count($this->filters[$filter])){
            return $this;
        }

        $queue = new \SplPriorityQueue();

        foreach($this->filters[$filter] as $index => $action){
            $queue->insert($index, $action["prio"]);
        }

        $queue->top();

        while($queue->valid()){

            $index = $queue->current();

            if (is_callable($this->filters[$filter][$index]["fn"])){
                $value = call_user_func_array($this->filters[$filter][$index]["fn"], [$value, $args]);
            }

            $queue->next();
        }

        return $value;
    }

    public function remove_all($filter) {
        $this->filters[$filter] = [];
    }

}