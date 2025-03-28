<?php
namespace Model;
use \PDO;
use \PDOStatement;

if (!defined('MAIN_SCRIPT')) die(__FILE__ . ':' . __LINE__ . ' says: Go away!' . ' ');


abstract class CRUDAbstract {

    public string $_pkName;
    public mixed $_pkValue;
    const _TableName = '';
    protected static string $_TableName = '';

    protected array $_meta;


    protected array $_dirty = [];
    protected array $_original = [];

    protected static int $_InstanceCount = 0;

    /**
     * @var PDO
     */
    private static \PDO $_pdo;

    /**
     * @var PDOStatement
     */
    private \PDOStatement $_stmt;


    function __construct() {
        $components = explode('\\', static::class);
        static::$_TableName = end($components);

        //TODO add init here
        //$this->getColumnMeta();

        if(static::_TableName === NULL || static::$_TableName === '') {
            ;
            //throw new DomainException(__CLASS__ . ' undefined  _TableName');
        }

        $classVariants = [
            'staticClass' => static::class,
            'selfClass' => self::class,
            'this' => $this::class,
            '__CLASS__' => __CLASS__,

        ];

        //++$this::$_InstanceCount;
        //++self::$_InstanceCount;
        ++static::$_InstanceCount;
        $this->connectStorage();
    }

    function __destruct()   {
        --$this::$_InstanceCount;
    }

    function connectStorage(): void {
        static::$_pdo = getVerbindung();
    }

    public function getTableName(): string {
        return defined(static::_TableName) ? static::_TableName : get_class($this);
    }

    /**
     * @return PDO
     */
    function getVerbindung(): PDO {
        return static::$_pdo;
    }

    /**
     *
     *
     * @param string $statement  The SQL as a string to prepare / execute
     * @return PDOStatement
     */
    function prepareStatement(string $statement): PDOStatement {
        // SQL-Abfrage vorbereiten
        $this->_stmt = $this::$_pdo->prepare($statement);

        return $this->_stmt;
    }

    protected function execute(): bool {
        // SQL-Abfrage ausführen
        return $this->_stmt->execute();
    }

    public function persist(): bool {
     $res = FALSE;
     if(empty($this->_pkValue)) {
         $res = $this->create();
         //FIXME exception handling here
         if($res)   {
             ;
         }
     } else {
         $res = $this->update();
         //FIXME exception handling here
         if($res)   {
             ;
         }
     }

     $this->clean();
     return $res;
    }

    /**
     * @param int $id
     * @return bool
     */
    public static function deleteById(int $id): bool {
        try {
            $pdo = getVerbindung();
            $stmt = $pdo->prepare("DELETE FROM ".static::_TableName." WHERE ID=:ID");

            $stmt->bindParam(':ID', $id, PDO::PARAM_INT);

            $stmt->execute();

            //FIXME error check
            return TRUE;
        } catch (PDOException $e) {
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();
        }
    }

    public function delete(): void {
        if($this->_pkName && $this->_pkValue)   {
            $this::deleteById($this->_pkValue);
        }
    }

    /**
     * "Declares" the object as clean (no pending change that need to be persisted)
     * Use with caution!
     *
     * @return void
     */
    protected function clean(): void {
        $this->_dirty = [];
        $this->_original = [];
    }

    public function getInstancesCount(): int {
        return $this::$_InstanceCount;
    }

    /**
     *	Automatically get column metadata
     */
    protected function getColumnMeta()
    {
        // Clear any previous column/field info
        $this->_fields = array();
        $this->_fieldMeta = array();
        $this->_primaryKey = NULL;

        // Automatically retrieve column information if column info not specified
        if(count($this->_fields) == 0 || count($this->_fieldMeta) == 0)
        {
            // Fetch all columns and store in $this->fields
            $columns = $this->db->query("SHOW COLUMNS FROM " . static::$_TableName, PDO::FETCH_ASSOC);
            foreach($columns as $key => $col)
            {
                // Insert into fields array
                $colname = $col['Field'];
                $this->_fields[$colname] = $col;
                if($col['Key'] == "PRI" && empty($this->_primaryKey)) {
                    $this->_primaryKey = $colname;
                }

                // Set field types
                $colType = $this->parseColumnType($col['Type']);
                $this->_fieldMeta[$colname] = $colType;
            }
        }
        return true;
    }

    /**
     *	Parse PDO-produced column type
     *	[internal function]
     */
    protected function parseColumnType($colType)
    {
        $colInfo = array();
        $colParts = explode(" ", $colType);
        if($fparen = strpos($colParts[0], "("))
        {
            $colInfo['type'] = substr($colParts[0], 0, $fparen);
            $colInfo['pdoType'] = '';
            $colInfo['length']  = str_replace(")", "", substr($colParts[0], $fparen+1));
            $colInfo['attributes'] = isset($colParts[1]) ? $colParts[1] : NULL;
        }
        else
        {
            $colInfo['type'] = $colParts[0];
        }

        // PDO Bind types
        $pdoType = '';
        foreach($this->_pdoBindTypes as $pKey => $pType)
        {
            if(strpos(' '.strtolower($colInfo['type']).' ', $pKey)) {
                $colInfo['pdoType'] = $pType;
                break;
            } else {
                $colInfo['pdoType'] = PDO::PARAM_STR;
            }
        }

        return $colInfo;
    }

    /**
     *	Will attempt to bind columns with datatypes based on parts of the column type name
     *	Any part of the name below will be picked up and converted unless otherwise sepcified
     * 	Example: 'VARCHAR' columns have 'CHAR' in them, so 'char' => PDO::PARAM_STR will convert
     *	all columns of that type to be bound as PDO::PARAM_STR
     *	If there is no specification for a column type, column will be bound as PDO::PARAM_STR
     */
    protected $_pdoBindTypes = array(
        'char' => PDO::PARAM_STR,
        'int' => PDO::PARAM_INT,
        'bool' => PDO::PARAM_BOOL,
        'date' => PDO::PARAM_STR,
        'time' => PDO::PARAM_INT,
        'text' => PDO::PARAM_STR,
        'blob' => PDO::PARAM_LOB,
        'binary' => PDO::PARAM_LOB
    );
}

