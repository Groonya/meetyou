Вместо wrk использовал vegeta (https://github.com/tsenart/vegeta) - думаю небольшая разница для нас.

Вывод vegeta до добавления индекса (для 1/10/100 одновременных запросов. При 1000 уже не тянет даже с индексами):

**1rps:**

    echo "GET http://localhost:8080/?q=zor" | vegeta attack -rate=1  -header 'Cookie: PHPSESSID=3fa63f4348293b99ccb1884d996b66da' -duration=1s -timeout=0 | vegeta report -type=text
    Requests      [total, rate, throughput]  1, 1.00, 1.00
    Duration      [total, attack, wait]      712.133682ms, 0s, 712.133682ms
    Latencies     [mean, 50, 95, 99, max]    712.133682ms, 712.133682ms, 712.133682ms, 712.133682ms, 712.133682ms
    Bytes In      [total, mean]              21817, 21817.00
    Bytes Out     [total, mean]              0, 0.00
    Success       [ratio]                    100.00%
    Status Codes  [code:count]               200:1

**10rps:**

    echo "GET http://localhost:8080/?q=zor" | vegeta attack -rate=10  -header 'Cookie: PHPSESSID=3fa63f4348293b99ccb1884d996b66da' -duration=1s -timeout=0 | vegeta report -type=text
    Requests      [total, rate, throughput]  10, 11.11, 1.55
    Duration      [total, attack, wait]      6.469849659s, 899.972586ms, 5.569877073s
    Latencies     [mean, 50, 95, 99, max]    3.132676206s, 3.133195751s, 5.569877073s, 5.569877073s, 5.569877073s
    Bytes In      [total, mean]              218170, 21817.00
    Bytes Out     [total, mean]              0, 0.00
    Success       [ratio]                    100.00%
    Status Codes  [code:count]               200:10

**100rps:**

    echo "GET http://localhost:8080/?q=zor" | vegeta attack -rate=100  -header 'Cookie: PHPSESSID=3fa63f4348293b99ccb1884d996b66da' -duration=1s -timeout=0 | vegeta report -type=text
    Requests      [total, rate, throughput]  100, 101.04, 1.54
    Duration      [total, attack, wait]      1m0.988285276s, 989.745748ms, 59.998539528s
    Latencies     [mean, 50, 95, 99, max]    32.124483641s, 32.2363133s, 59.998226509s, 59.999102143s, 59.999217475s
    Bytes In      [total, mean]              2051806, 20518.06
    Bytes Out     [total, mean]              0, 0.00
    Success       [ratio]                    94.00%
    Status Codes  [code:count]               200:94  504:6
    Error Set:
    504 Gateway Time-out

### Запрос создания индексов:

```sql
create index users_firstname_index on users (name_first);
create index users_lastname_index on users (name_last);
```

### EXPLAIN
```sql
EXPLAIN SELECT u.id, u.name_first, u.name_last FROM users u WHERE u.name_first LIKE 'zor%' OR u.name_last LIKE 'zor%' ORDER BY id LIMIT 0, 100;
```

| id | select_type | table | partitions | type        | possible_keys                              | key                                        | key_len | ref  | rows | filtered | Extra                                                                                     |
|----|-------------|-------|------------|-------------|--------------------------------------------|--------------------------------------------|---------|------|------|----------|-------------------------------------------------------------------------------------------|
|  1 | SIMPLE      | u     | NULL       | index_merge | users_firstname_index,users_lastname_index | users_firstname_index,users_lastname_index | 202,202 | NULL |  338 |   100.00 | Using sort_union(users_firstname_index,users_lastname_index); Using where; Using filesort |

Так как мы используем OR и LIKE по префиксу, то составной индекс тут не подойдет. Поэтому делаем по индексу на каждое поле. Еще можно переписать запрос на UNION, но по факту получится тоже самое что с index_merge. Ну или можно выполнить два отдельных запроса и смержить результат на уровне приложения.

##Графики

![Latency](https://github.com/Groonya/meetyou/blob/master/vegeta/report/01_indexes/latency.png)

![Throughput](https://github.com/Groonya/meetyou/blob/master/vegeta/report/01_indexes/throughput.png)
