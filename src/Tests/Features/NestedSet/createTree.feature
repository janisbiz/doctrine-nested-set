Feature: Create Tree
  Scenario:
    Given I want to create 5 root node entities with created at date "2018-08-31 00:00:00"
    When I create a new tree root nodes
    Then I should get these rows in database:
    |id|tree_scope_id|tree_left|tree_right|tree_level|created_at         |
    |1 |1            |0        |1         |0         |2018-08-31 00:00:00|
    |2 |2            |0        |1         |0         |2018-08-31 00:00:00|
    |3 |3            |0        |1         |0         |2018-08-31 00:00:00|
    |4 |4            |0        |1         |0         |2018-08-31 00:00:00|
    |5 |5            |0        |1         |0         |2018-08-31 00:00:00|