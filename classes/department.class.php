<?php
  declare(strict_types = 1);
  require_once(__DIR__ . '/ticket.class.php');
  require_once(__DIR__ . '/user.class.php');

  class Department
  {
    public string $category;
    public bool $hasPhoto;
    public array $tickets;
    public array $members;

    public function __construct(string $category, array $tickets, array $members) {
      $this->category = $category;
      $this->tickets = $tickets;
      $this->members = $members;
      $this->hasPhoto = $this->getPhoto() != '../images/departments/default.png';
    }

    static function getMembers(PDO $db, string $category): array {
      $stmt = $db->prepare('
          SELECT userId, name, username, email, password, reputation, type
          FROM User JOIN AgentDepartment ON User.userId = AgentDepartment.agent
          WHERE AgentDepartment.department = ?
      ');

      $stmt->execute(array($category));

      $members = array();
      while ($member = $stmt->fetch()) {
        $members[] = new User(
          intval($member['userId']),
          $member['name'],
          $member['username'],
          $member['email'],
          $member['password'],
          intval($member['reputation']),
          $member['type'],
        );
      }

      return $members;
    }

    static function addDepartment(PDO $db, string $new_category) {
      $stmt = $db->prepare('INSERT INTO Department (category) VALUES (?)');
      $stmt->execute(array($new_category));
    }

    static function removeDepartment(PDO $db, string $category) {
      $stmt = $db->prepare('DELETE FROM Department WHERE category = ?');
      $stmt->execute(array($category));
    }

    static function getDepartments(PDO $db): array {
      $stmt = $db->prepare('SELECT category FROM Department');
      $stmt->execute();

      $departments = array();
      while ($department = $stmt->fetch()) {
        $departments[] = new Department(
          $department['category'],
          Department::getTickets($db, $department['category']),
          Department::getMembers($db, $department['category'])
        );
      }

      return $departments;
    }

    static function getDepartment(PDO $db, string $category): Department {
      $stmt = $db->prepare('SELECT category FROM Department WHERE category = ?');
      $stmt->execute(array($category));
      $department = $stmt->fetch();

      return new Department(
        $department['category'],
        Department::getTickets($db, $department['category']),
        Department::getMembers($db, $department['category'])
      );
    }

    function addMember(PDO $db, int $userId) {
      $stmt = $db->prepare('INSERT INTO AgentDepartment (agent, department) VALUES (?, ?);');
      $stmt->execute(array($userId, $this->category));
    }

    function getPhoto(): string {
      $fileName = strtolower(str_replace(" ", "_", $this->category));
      $default = "../images/departments/default.png";
      $attemp = "../images/departments/" . $fileName . ".png";
      
      if (file_exists($attemp)) return $attemp;
      else return $default;
    }

    static function getTickets(PDO $db, string $category): array {
      $stmt = $db->prepare('SELECT * FROM Ticket WHERE category = ?');
      $stmt->execute(array($category));

      $tickets = array();
      while ($ticket = $stmt->fetch()) {
        $replier = ($ticket['replier']) ? $ticket['replier'] : 0;
        $tickets[] = new Ticket(
          intval($ticket['id']),
          htmlentities($ticket['title']),
          htmlentities($ticket['text']),
          $ticket['createDate'],
          $ticket['visibility'],
          substr($ticket['priority'], 2),
          $ticket['status'],
          $ticket['category'],
          Ticket::getTicketTags($db, $ticket['id']),
          User::getUser($db, $ticket['creator']),
          User::getUser($db, $replier),
          intval($ticket['feedback'])
        );
      }
      return $tickets;
    }

    function getStats($db, $field): array {
      if ($field !== 'priority' && $field !== 'status') return array();

      $stmt = $db->prepare('SELECT ' . $field . ' as label, COUNT(*) as count FROM Ticket WHERE category = ? GROUP BY ' . $field);
      $stmt->execute(array($this->category));

      $stats = array();
      while ($stat = $stmt->fetch()) {
        if ($field === 'priority')
          $stats[] = array(substr($stat['label'], 2), $stat['count']);
        else 
          $stats[] = array($stat['label'], $stat['count']);
      }
      return $stats;
    }

  }
?>