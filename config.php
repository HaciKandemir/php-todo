<?php 
class Config {
	
	private $dbPath = 'data'.DIRECTORY_SEPARATOR;
    private $dbFile;

	public function __construct(string $dbFile)
	{
		$this->dbFile = $dbFile;
		$dirCheck = is_dir($this->dbPath);
        $dbCheck = file_exists($this->dbPath . $this->dbFile . '.json');
        if (!$dirCheck){
            $this->dirCreate();
        }
        if (!$dbCheck){
            $this->dbCreate($this->dbFile);
        }
	}

	private function dirCreate(){
		mkdir($dbPath);
	}

	private function dbCreate(string $dbName){
		file_put_contents($this->dbPath . $dbName . '.json', json_encode([]));
	}

	public function getDbFile() : string {
        return $this->dbPath . $this->dbFile . '.json';
    }
}
?>