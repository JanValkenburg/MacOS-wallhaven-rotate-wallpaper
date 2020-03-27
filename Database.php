<?php

/**
 * Class Database
 */
class Database
{

    /** @var PDO */
    protected $pdo;

    public function __construct(string $file)
    {
        if (file_exists($file)) {
            $this->pdo = new \PDO("sqlite:".$file);
        } else {
            $this->pdo = new \PDO("sqlite:".$file);
            $this->install();
        }
    }

    public function install() {
        $this->pdo->exec('CREATE TABLE `ignore` (id CHAR(6) UNIQUE);');
    }

    public function getIgnoreImages() {
        $stmt = $this->pdo->prepare('SELECT id FROM `ignore`');
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return array_flip($res);
    }

    /**
     * @param $id
     */
    public function ignoreImage(string $id) {
        $stmt = $this
            ->pdo
            ->prepare('
                INSERT INTO `ignore` (`id`) VALUES (:id)
            ');
        $stmt->execute(['id' => $id]);
    }

}