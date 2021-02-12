<?php
class TodoList {

    private $todolistName;
    private array $myTodoList;
    private $db;

    // init
    public function __construct(string $todoListName)
    {
        $this->todolistName = $todoListName;
        $this->create();
        $this->myTodoList = json_decode(file_get_contents($this->db));
    }

    public function __destruct() {
    	$this->myTodoList = json_decode(file_get_contents($this->db));
    }

    public function getTodos() : array {
        return $this->myTodoList;
    }

    // create todolist
    private function create(){
    	$conf = new Config($this->todolistName);
        $this->db = $conf->getDbFile();
    }

    // add
    public function add(string $task, int $status=0) : void{
        if (!empty($task)){
            $this->myTodoList[] = [$task,$status];
            $this->save();
        }
    }

    // delete
    public function delete(int $id){
        $id--;
        unset($this->myTodoList[$id]);
        $this->myTodoList = array_values( $this->myTodoList );
        $this->save();
    }

    // update
    public function update(int $id, string $task, int $status){
    	if (!empty($task) && !empty($id)) {
    		$id--;
    		$this->myTodoList[$id][0] = $task;
    		$this->myTodoList[$id][1] = $status;
    		$this->save();
    	}
    }

    // sort change
    public function customSort(int $oldPosition, int $newPosition){
        $temp = $this->myTodoList[$oldPosition];    
        if ($newPosition>$oldPosition) {
            // yukarıdan aşağıya taşıma    
            for ($i=$oldPosition; $i < $newPosition; $i++) { 
                $this->myTodoList[$i] = $this->myTodoList[$i+1];
            }
        }elseif($newPosition<$oldPosition){
            // aşağıdan yukarıya taşıma
            for ($i=$oldPosition; $i > $newPosition; $i--) { 
                $this->myTodoList[$i] = $this->myTodoList[$i-1]; 
            }
        }
        $this->myTodoList[$newPosition] = $temp;
        $this->save();
    }

    //get db file name
    public function getTodoName(){
        return $this->todolistName;
    }

    // save file
    public function save(){
        file_put_contents($this->db, json_encode($this->myTodoList));
        header('location:/');
    }
}

?>