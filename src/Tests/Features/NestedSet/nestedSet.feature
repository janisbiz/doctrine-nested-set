Feature: Nested Set

  Scenario: Create root nodes
    Given I want to create 5 root node entities with created at date "2018-08-31 00:00:00"
    When I create a new tree root nodes
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 1          | 0          | 2018-08-31 00:00:00 |
      | 2  | 2             | 0         | 1          | 0          | 2018-08-31 00:00:00 |
      | 3  | 3             | 0         | 1          | 0          | 2018-08-31 00:00:00 |
      | 4  | 4             | 0         | 1          | 0          | 2018-08-31 00:00:00 |
      | 5  | 5             | 0         | 1          | 0          | 2018-08-31 00:00:00 |

  Scenario: Create root node with children
    Given I want to create 1 root node entities with created at date "2018-08-31 01:00:00"
    When I create a new tree root nodes
    And I add 1 children with created at date "2018-08-31 02:00:00" on root node
    And I add 1 children with created at date "2018-08-31 03:00:00" on root node
    And I add 1 children with created at date "2018-08-31 04:00:00" on root node
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 7          | 0          | 2018-08-31 01:00:00 |
      | 2  | 1             | 1         | 2          | 1          | 2018-08-31 02:00:00 |
      | 3  | 1             | 3         | 4          | 1          | 2018-08-31 03:00:00 |
      | 4  | 1             | 5         | 6          | 1          | 2018-08-31 04:00:00 |

  Scenario: Create root node with children and more than two levels
    Given I want to create 1 root node entities with created at date "2018-08-31 05:00:00"
    When I create a new tree root nodes
    And I add 2 children with created at date "2018-08-31 06:00:00" on root node
    And I add 3 children with created at date "2018-08-31 07:00:00" of nodes with level 1
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 17         | 0          | 2018-08-31 05:00:00 |
      | 2  | 1             | 1         | 8          | 1          | 2018-08-31 06:00:00 |
      | 3  | 1             | 9         | 16         | 1          | 2018-08-31 06:00:00 |
      | 4  | 1             | 2         | 3          | 2          | 2018-08-31 07:00:00 |
      | 5  | 1             | 4         | 5          | 2          | 2018-08-31 07:00:00 |
      | 6  | 1             | 6         | 7          | 2          | 2018-08-31 07:00:00 |
      | 7  | 1             | 10        | 11         | 2          | 2018-08-31 07:00:00 |
      | 8  | 1             | 12        | 13         | 2          | 2018-08-31 07:00:00 |
      | 9  | 1             | 14        | 15         | 2          | 2018-08-31 07:00:00 |

  Scenario: Create root nodes with children and add more children on multiple levels
    Given I want to create 2 root node entities with created at date "2018-08-31 08:00:00"
    When I create a new tree root nodes
    And I add 2 children with created at date "2018-08-31 09:00:00" on root node
    And I add 1 children with created at date "2018-08-31 10:00:00" of nodes with level 1
    And I add 2 children with created at date "2018-08-31 11:00:00" of nodes with level 2
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 17         | 0          | 2018-08-31 08:00:00 |
      | 2  | 2             | 0         | 17         | 0          | 2018-08-31 08:00:00 |
      | 3  | 1             | 1         | 8          | 1          | 2018-08-31 09:00:00 |
      | 4  | 1             | 9         | 16         | 1          | 2018-08-31 09:00:00 |
      | 5  | 2             | 1         | 8          | 1          | 2018-08-31 09:00:00 |
      | 6  | 2             | 9         | 16         | 1          | 2018-08-31 09:00:00 |
      | 7  | 1             | 2         | 7          | 2          | 2018-08-31 10:00:00 |
      | 8  | 1             | 10        | 15         | 2          | 2018-08-31 10:00:00 |
      | 9  | 2             | 2         | 7          | 2          | 2018-08-31 10:00:00 |
      | 10 | 2             | 10        | 15         | 2          | 2018-08-31 10:00:00 |
      | 11 | 1             | 3         | 4          | 3          | 2018-08-31 11:00:00 |
      | 12 | 1             | 5         | 6          | 3          | 2018-08-31 11:00:00 |
      | 13 | 1             | 11        | 12         | 3          | 2018-08-31 11:00:00 |
      | 14 | 1             | 13        | 14         | 3          | 2018-08-31 11:00:00 |
      | 15 | 2             | 3         | 4          | 3          | 2018-08-31 11:00:00 |
      | 16 | 2             | 5         | 6          | 3          | 2018-08-31 11:00:00 |
      | 17 | 2             | 11        | 12         | 3          | 2018-08-31 11:00:00 |
      | 18 | 2             | 13        | 14         | 3          | 2018-08-31 11:00:00 |

  Scenario: Create root node with children and insert new node after specific ID
    Given I want to create 1 root node entities with created at date "2018-08-31 12:00:00"
    When I create a new tree root nodes
    And I add 1 children with created at date "2018-08-31 13:00:00" on root node
    And I add 1 children with created at date "2018-08-31 14:00:00" on root node
    And I add 2 children with created at date "2018-08-31 15:00:00" after node with id 2
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 9          | 0          | 2018-08-31 12:00:00 |
      | 2  | 1             | 1         | 2          | 1          | 2018-08-31 13:00:00 |
      | 3  | 1             | 7         | 8          | 1          | 2018-08-31 14:00:00 |
      | 4  | 1             | 5         | 6          | 1          | 2018-08-31 15:00:00 |
      | 5  | 1             | 3         | 4          | 1          | 2018-08-31 15:00:00 |

  Scenario: Create root node with children and insert new node before specific ID
    Given I want to create 1 root node entities with created at date "2018-08-31 16:00:00"
    When I create a new tree root nodes
    And I add 1 children with created at date "2018-08-31 17:00:00" on root node
    And I add 1 children with created at date "2018-08-31 18:00:00" on root node
    And I add 2 children with created at date "2018-08-31 19:00:00" before node with id 2
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 9          | 0          | 2018-08-31 16:00:00 |
      | 2  | 1             | 5         | 6          | 1          | 2018-08-31 17:00:00 |
      | 3  | 1             | 7         | 8          | 1          | 2018-08-31 18:00:00 |
      | 4  | 1             | 1         | 2          | 1          | 2018-08-31 19:00:00 |
      | 5  | 1             | 3         | 4          | 1          | 2018-08-31 19:00:00 |

  Scenario: Create root nodes with children and move nodes after/before specific ID on same level
    Given I want to create 2 root node entities with created at date "2018-08-31 20:00:00"
    When I create a new tree root nodes
    And I add 1 children with created at date "2018-08-31 21:00:00" on root node
    And I add 1 children with created at date "2018-08-31 22:00:00" on root node
    And I add 1 children with created at date "2018-08-31 23:00:00" of nodes with level 1
    And I add 2 children with created at date "2018-09-01 00:00:00" after node with id 3
    And I add 2 children with created at date "2018-09-01 01:00:00" before node with id 3
    And I add 2 children with created at date "2018-09-01 02:00:00" after node with id 10
    And I add 2 children with created at date "2018-09-01 03:00:00" before node with id 16
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 17         | 0          | 2018-08-31 20:00:00 |
      | 2  | 2             | 0         | 17         | 0          | 2018-08-31 20:00:00 |
      | 3  | 1             | 5         | 8          | 1          | 2018-08-31 21:00:00 |
      | 4  | 2             | 1         | 4          | 1          | 2018-08-31 21:00:00 |
      | 5  | 1             | 13        | 16         | 1          | 2018-08-31 22:00:00 |
      | 6  | 2             | 5         | 16         | 1          | 2018-08-31 22:00:00 |
      | 7  | 1             | 6         | 7          | 2          | 2018-08-31 23:00:00 |
      | 8  | 2             | 2         | 3          | 2          | 2018-08-31 23:00:00 |
      | 9  | 1             | 14        | 15         | 2          | 2018-08-31 23:00:00 |
      | 10 | 2             | 6         | 7          | 2          | 2018-08-31 23:00:00 |
      | 11 | 1             | 11        | 12         | 1          | 2018-09-01 00:00:00 |
      | 12 | 1             | 9         | 10         | 1          | 2018-09-01 00:00:00 |
      | 13 | 1             | 1         | 2          | 1          | 2018-09-01 01:00:00 |
      | 14 | 1             | 3         | 4          | 1          | 2018-09-01 01:00:00 |
      | 15 | 2             | 14        | 15         | 2          | 2018-09-01 02:00:00 |
      | 16 | 2             | 12        | 13         | 2          | 2018-09-01 02:00:00 |
      | 17 | 2             | 8         | 9          | 2          | 2018-09-01 03:00:00 |
      | 18 | 2             | 10        | 11         | 2          | 2018-09-01 03:00:00 |

  Scenario: Create root nodes with children and move nodes after/before specific ID on same level and different levels
    Given I want to create 1 root node entities with created at date "2018-09-01 04:00:00"
    When I create a new tree root nodes
    And I add 2 children with created at date "2018-09-01 05:00:00" on root node
    And I add 3 children with created at date "2018-09-01 06:00:00" of nodes with level 1
    And I move node with id 9 before node with id 7
    And I move node with id 9 after node with id 8
    And I move node with id 4 after node with id 9
    And I move node with id 9 before node with id 3
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 17         | 0          | 2018-09-01 04:00:00 |
      | 2  | 1             | 1         | 6          | 1          | 2018-09-01 05:00:00 |
      | 3  | 1             | 9         | 16         | 1          | 2018-09-01 05:00:00 |
      | 4  | 1             | 14        | 15         | 2          | 2018-09-01 06:00:00 |
      | 5  | 1             | 2         | 3          | 2          | 2018-09-01 06:00:00 |
      | 6  | 1             | 4         | 5          | 2          | 2018-09-01 06:00:00 |
      | 7  | 1             | 10        | 11         | 2          | 2018-09-01 06:00:00 |
      | 8  | 1             | 12        | 13         | 2          | 2018-09-01 06:00:00 |
      | 9  | 1             | 7         | 8          | 1          | 2018-09-01 06:00:00 |

  Scenario: Create root nodes with children and add more children on multiple levels and remove few nodes
    Given I want to create 2 root node entities with created at date "2018-09-01 07:00:00"
    When I create a new tree root nodes
    And I add 2 children with created at date "2018-09-01 08:00:00" on root node
    And I add 1 children with created at date "2018-09-01 09:00:00" of nodes with level 1
    And I add 2 children with created at date "2018-09-01 10:00:00" of nodes with level 2
    And I remove node with id 18
    And I remove node with id 8
    Then I should get these rows in database:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 1  | 1             | 0         | 11         | 0          | 2018-09-01 07:00:00 |
      | 2  | 2             | 0         | 15         | 0          | 2018-09-01 07:00:00 |
      | 3  | 1             | 1         | 8          | 1          | 2018-09-01 08:00:00 |
      | 4  | 1             | 9         | 10         | 1          | 2018-09-01 08:00:00 |
      | 5  | 2             | 1         | 8          | 1          | 2018-09-01 08:00:00 |
      | 6  | 2             | 9         | 14         | 1          | 2018-09-01 08:00:00 |
      | 7  | 1             | 2         | 7          | 2          | 2018-09-01 09:00:00 |
      | 9  | 2             | 2         | 7          | 2          | 2018-09-01 09:00:00 |
      | 10 | 2             | 10        | 13         | 2          | 2018-09-01 09:00:00 |
      | 11 | 1             | 3         | 4          | 3          | 2018-09-01 10:00:00 |
      | 12 | 1             | 5         | 6          | 3          | 2018-09-01 10:00:00 |
      | 15 | 2             | 3         | 4          | 3          | 2018-09-01 10:00:00 |
      | 16 | 2             | 5         | 6          | 3          | 2018-09-01 10:00:00 |
      | 17 | 2             | 11        | 12         | 3          | 2018-09-01 10:00:00 |

  Scenario: Create root nodes with children and add more children on multiple levels and load a tree
    Given I want to create 2 root node entities with created at date "2018-08-31 11:00:00"
    When I create a new tree root nodes
    And I add 2 children with created at date "2018-08-31 12:00:00" on root node
    And I add 1 children with created at date "2018-08-31 13:00:00" of nodes with level 1
    And I add 2 children with created at date "2018-08-31 14:00:00" of nodes with level 2
    And I load tree with scope id 2
    Then I should get these rows in database filtered by scope id 2:
      | id | tree_scope_id | tree_left | tree_right | tree_level | created_at          |
      | 2  | 2             | 0         | 17         | 0          | 2018-08-31 11:00:00 |
      | 5  | 2             | 1         | 8          | 1          | 2018-08-31 12:00:00 |
      | 6  | 2             | 9         | 16         | 1          | 2018-08-31 12:00:00 |
      | 9  | 2             | 2         | 7          | 2          | 2018-08-31 13:00:00 |
      | 10 | 2             | 10        | 15         | 2          | 2018-08-31 13:00:00 |
      | 15 | 2             | 3         | 4          | 3          | 2018-08-31 14:00:00 |
      | 16 | 2             | 5         | 6          | 3          | 2018-08-31 14:00:00 |
      | 17 | 2             | 11        | 12         | 3          | 2018-08-31 14:00:00 |
      | 18 | 2             | 13        | 14         | 3          | 2018-08-31 14:00:00 |
