<?php

class Department
{
  public string $category;

  public function __construct(string $category)
  {
    $this->category = $category;
  }

  function getMembers(PDO $db): array
  {
    $stmt = $db->prepare('
        SELECT userId, name, username, email, password, reputation, type
        FROM User JOIN AgentDepartment ON User.userId = AgentDepartment.agent
        WHERE AgentDepartment.department = ?
      ');

    $stmt->execute(array($this->category));

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

  static function addDepartment(PDO $db, string $new_category){
    $stmt = $db->prepare('INSERT INTO Department (category) VALUES (?)');
    $stmt->execute(array($new_category));
  }

  static function getDepartments(PDO $db): array
  {
    $stmt = $db->prepare('SELECT category FROM Department');
    $stmt->execute();

    $departments = array();
    while ($department = $stmt->fetch()) {
      $departments[] = new Department(
        $department['category']
      );
    }

    return $departments;
  }

  static function getDepartment(PDO $db, string $category): Department
  {
    $stmt = $db->prepare('
        SELECT category
        FROM Department 
        WHERE category = ?
      ');

    $stmt->execute(array($category));
    $department = $stmt->fetch();

    return new Department(
      $department['category'],
    );
  }

  function addMember(PDO $db, int $userId)
  {
    $stmt = $db->prepare('INSERT INTO AgentDepartment (agent, department) VALUES (?, ?);');
    $stmt->execute(array($userId, $this->category));
  }
}
?>