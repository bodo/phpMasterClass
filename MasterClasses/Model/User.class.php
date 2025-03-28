<?php

namespace Model;
use Interfaces\SQLInterface;
use \PDO;
use \PDOException;

if (!defined('MAIN_SCRIPT')) die(__FILE__ . ':' . __LINE__ . ' says: Go away!' . ' ');

/**
 *
 *
 * @property string firstname
 * @property string lastname
 * @property string zuname
 * @property int age
 * @property int ID
 */
class User extends CRUDAbstract implements SQLInterface
{

    protected static int $_InstanceCount = 0;

    const string _PKFieldName = 'ID';
    const string _TableName = 'User';


    protected int $ID;

    /**
     * @var string $firstname
     */
    protected string $firstname;

    /**
     * @var string $lastname
     */
    protected string $lastname;

    /**
     * @var integer $age
     */
    protected int $age;


    /**
     *
     * @param ?string $firstname
     * @param ?string $lastname
     * @param ?integer $age
     * @param ?integer $ID
     */
    public function __construct(?string $firstname='', ?string $lastname='', ?int $age=0, ?int $ID = NULL)
    {
        parent::__construct();

        $this->_pkName = static::_PKFieldName;


        if ($ID !== NULL) {
            $this->_pkValue = $ID;
        }

        if(func_num_args()) {
            if($ID !== NULL && $ID > 0) {
                $ID && $this->readUser(ID: $ID);
            } else {
                $lastname && $this->setLastname($lastname);
                $firstname && $this->setFirstname($firstname);
                $age > 0 && $this->setAge($age);
            }
        } else {
            // PDO fetchObject calls us without parameters, but pre-injected data into our props
            //FIXME anything to be done here? hmm
            //TODO: hydrate sub-objects
        }
    }



    /**
     * @param int $id
     * @return void
     */
    public static function findByID(int $id): ?User  {
        //FIXME ugly "$this instanceof static" code, works but IDE doesn't like it
        $user = new User(ID: $id);
        //$user->readUser(ID: $id);
        return $user;
    }

    /**
     * Just an Alias of findByID for consistency
     *
     * @param int $id
     * @return void
     */
    public function findOneByID(int $id): void  {
        $this->findByID(id: $id);
    }

    /**
     * @param string $firstname
     * @return void
     */
    public function findOneByFirstname(string $firstname): void     {
        $this->readUser(firstname: $firstname);
    }

    /**
     * @param string $lastname
     * @return void
     */
    public function findOneByLastname(string $lastname): void   {
        $this->readUser(lastname: $lastname);
    }

    /**
     * @param int $age
     * @return void
     */
    public function findOneByAge(int $age): void    {   $this->readUser(age: $age);     }




    //FIXME: how on earth can we define this protected
    //without getting such error: PHP Fatal error:  Access level to User::create() must be public (as in class SQLInterface)
    //methods declarations in interfaces MUST also be public!
    public function create(): bool
    {
        try {
            $pdo = $this->getVerbindung();
            $stmt = $pdo->prepare("INSERT INTO " . $this::_TableName . " (firstname, lastname, age) 
                                    VALUES (:firstname, :lastname, :age)");

            $stmt->bindParam(':firstname', $this->firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $this->lastname, PDO::PARAM_STR);
            $stmt->bindParam(':age', $this->age, PDO::PARAM_INT);

            $stmt->execute();

            $this->_pkValue = $pdo->lastInsertId();
            $this->{$this->_pkName} = $this->_pkValue;

            // return TRUE on success (lastInsertId is set)
            return !empty($this->_pkValue);

        } catch (PDOException $e) {
            // Fehler abfangen und anzeigen
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();
        }

        //FIXME this code is not reached, but satisfies my IDE
        return FALSE;
    }

    /**
     * Gives you a User Object that is already saved in the DB.
     *
     * @param string|null $firstname
     * @param string|null $lastname
     * @param int|null $age
     * @param int|null $ID
     * @return User
     */
    static public function factory(?string $firstname, ?string $lastname, ?int $age, ?int $ID = NULL): User {
        $user = new User($firstname, $lastname, $age);
        $user->persist();

        return $user;
    }


    /**
     * @return int|null
     */
    public function update(): ?int  {

        //anything need to be saved at all?
        if (count($this->_dirty) == 0)      return FALSE;

        try {
            $pdo = $this->getVerbindung();

            $sets = [];
            foreach ($this->_dirty as $key => $dummy) {

                $value = $this->{$key};

                $sets[$key] = ['key' => $key,
                    'value' => $value,
                    //'type' => PDO::PARAM_STR,
                    'sql' => "$key = :$key"
                ];

                $valueType = gettype($this->{$key});

                switch ($valueType) {
                    case 'integer':
                        $sets[$key]['type'] = PDO::PARAM_INT;
                        break;
                    case 'boolean';
                        //FIXME do we need a conversion to int ?
                        $sets[$key]['type'] = PDO::PARAM_BOOL;
                        break;
                    case 'DateTime':
                        $sets[$key]['value'] = $value->format('Y-m-d H:i:s');
                        $sets[$key]['type'] = PDO::PARAM_STR;
                        break;
                    case 'string':
                    default:
                        $sets[$key]['type'] = PDO::PARAM_STR;
                        break;
                }
            }

            $sqlSets = implode(', ', array_column($sets, 'sql'));

            $sqlWhere = '1=1';
            if (!empty($this->_pkValue)) {
                $sqlWhere .= ' AND ' . $this->_pkName . " = " . $this->_pkValue;

                $sets[$this->_pkName] = [
                    'key' => $this->_pkName,
                    'value' => $this->_pkValue,
                    'type' => PDO::PARAM_INT,
                    'sql' => "$this->_pkName = :$this->_pkName"
                ];
            }

            // possible errors:
            // 1. break a CONSTRAINT (PK given but taken, UNIQUE column)
            // 2. someone changed the Table structure (mismatch fields-count/-type)
            $stmt = $pdo->prepare("UPDATE " . static::_TableName . " SET " . $sqlSets
                                . " WHERE " . $sqlWhere);

            foreach ($sets as $key => $attr) {
                $stmt->bindParam($attr['key'], $attr['value'], $attr['type']);
            }


            $stmt->execute();
            return $stmt->rowCount();

        } catch (PDOException $e) {
            // Fehler abfangen und anzeigen
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();

            return 0;
        }
    }

    function deleteUser(string $lastname, string $firstname, int $age): void
    {
        try {
            $pdo = getVerbindung();
            $stmt = $pdo->prepare("DELETE FROM ".static::_TableName
                ." WHERE firstname=:firstname AND lastname=:lastname AND age=:age");

            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            // Fehler abfangen und anzeigen
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();
        }
    }


    protected function prepareBindAttributes(array $bindSets): void
    {
        foreach ($bindSets as $key => $set) {

        }
    }


    function createUser(string $lastname, string $firstname, int $age): void
    {
        try {
            $pdo = getVerbindung();
            $stmt = $pdo->prepare("INSERT INTO ".static::_TableName." (firstname, lastname, age) 
                                    VALUES (:firstname, :lastname, :age)");

            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            // Fehler abfangen und anzeigen
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();
        }
    }


    function readUser(?string $firstname = NULL, ?string $lastname = NULL, ?int $age = NULL, ?int $ID = NULL): ?static
    {
        try {
            $pdo = getVerbindung();

            $cond = [];

            if ($ID !== NULL) {
                $cond[] = ['key' => 'ID', 'value' => $ID,
                    'type' => PDO::PARAM_INT,
                    'sql' =>  static::_TableName.'.ID = :ID'];
            }

            if ($age !== NULL) {
                $cond[] = ['key' => 'age', 'value' => $age,
                    'type' => PDO::PARAM_INT,
                    'sql' => 'age = :age'];
            }

            if ($firstname !== NULL) {
                $cond[] = ['key' => 'firstname', 'value' => $firstname,
                    'type' => PDO::PARAM_STR,
                    'sql' => 'firstname = :firstname'];
            }

            if ($lastname !== NULL) {
                $cond[] = ['key' => 'lastname', 'value' => $lastname,
                    'type' => PDO::PARAM_STR,
                    'sql' => 'lastname = :lastname'];
            }

            $sql = implode(' AND ', array_column($cond, 'sql'));

            $join = '';
            if(FALSE) {
                $join = ' LEFT JOIN LoginLog ON User.ID = LoginLog.UserID ';
            }
            $stmt = $pdo->prepare('SELECT * FROM '. static::_TableName .$join.' WHERE '. $sql);

            foreach ($cond as $key => $condition) {
                $stmt->bindParam($condition['key'], $condition['value'], $condition['type']);
            }

            $stmt->execute();

            foreach(range(0, $stmt->columnCount() - 1) as $column_index)
            {
                $this->_meta[] = $stmt->getColumnMeta($column_index);
            }



            $userAsObject = $stmt->fetchObject(static::class);
            //$userAsArray = $stmt->fetch(PDO::FETCH_ASSOC);

            return $userAsObject;

        } catch (PDOException $e) {
            // Fehler abfangen und anzeigen
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();

            return NULL;
        }
    }

    function updateUser(string $lastname, string $firstname, int $age, ?int $ID = NULL): void
    {
        try {
            $pdo = getVerbindung();
            $stmt = $pdo->prepare("UPDATE ".static::_TableName." SET firstname=:firstname, lastname=:lastname, age=:age
                                    WHERE ID=:ID");

            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $stmt->bindParam(':age', $age, PDO::PARAM_INT);
            $stmt->bindParam(':ID', $ID, PDO::PARAM_INT);

            $stmt->execute();
        } catch (PDOException $e) {
            // Fehler abfangen und anzeigen
            echo "Fehler bei der Verbindung zur Datenbank: " . $e->getMessage();
        }
    }


    /**
     * @return string
     */
    public function getFirstname(): string  {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname(string $firstname): void
    {
        if (!empty($this->firstname)) {
            $this->_original['firstname'] = $this->firstname;
        }
        $this->_dirty['firstname'] = TRUE;
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname(): string   {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname(string $lastname): void
    {
        if (!empty($this->lastname)) {
            $this->_original['lastname'] = $this->lastname;
        }
        $this->_dirty['lastname'] = TRUE;
        $this->lastname = $lastname;
    }

    /**
     * @return integer
     */
    public function getAge(): int   {
        return $this->age;
    }

    /**
     * @param integer $age
     */
    public function setAge(int $age): void  {
        if (!empty($this->age)) {
            $this->_original['age'] = $this->age;
        }
        $this->_dirty['age'] = TRUE;
        $this->age = $age;
    }

    public function __toString(): string    {
        return "\nVorname: " . $this->firstname . " Nachname: " . $this->lastname . " Alter: " . $this->age . "\n";
    }

    public static function CountInstances(): int    {
        static $count = 0;

        return ++$count;
    }
}
