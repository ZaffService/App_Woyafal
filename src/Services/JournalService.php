<?php
namespace App\Services; // Corrige aussi le namespace (App et non APP)
use App\Repositories\JournalRepository;
use App\Entities\JournalEntity; // Assure l'import de JournalEntity

class JournalService 
{
    private static ?JournalService $journalService = null;
    private JournalRepository $journalRepository;

    public static function getInstance()
    {
        if (is_null(self::$journalService)) {
            self::$journalService = new JournalService(); 
        }
        return self::$journalService;
    }

    private function __construct()
    {
        $this->journalRepository = JournalRepository::getInstance();
    }

    public function create(JournalEntity $journalEntity)
    {
        return $this->journalRepository->insertJournal($journalEntity);   
    }
}
