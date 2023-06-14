<?php
class Poll
{
    public $id;
    public $question;
    public $options;
    public $isMultiple;
    public $createdAt;
    public $deadline;
    public $voters;
    public $given_answers;
    public function __construct($question, $options, $isMultiple, $deadline)
    {
        $jsonstorage = new JsonStorage("polls.json");
        $array = $jsonstorage->all();
        $i = 1;
        while (isset($array['poll' . $i])) {
            $i++;
        }
        $this->id = 'poll' . $i;
        $this->question = $question;
        $this->options = $options;
        $this->isMultiple = $isMultiple;
        $this->createdAt = date("Y-m-d");
        $this->deadline = $deadline;
        $this->voters = [];
        $this->given_answers = [];
        foreach ($options as $option) {
            $this->given_answers[$option] = 0;
        }
    }
    public function set_id($id)
    {
        $this->id = $id;
    }
    public function set_voters($voters)
    {
        $this->voters = $voters;
    }
    public function set_createdAt($date)
    {
        $this->createdAt = $date;
    }
    public function set_given_answers($answers)
    {
        $this->given_answers = $answers;
    }
    public function add_voters($voter)
    {
        array_push($this->voters, $voter);
    }
}
