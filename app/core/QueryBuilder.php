<?php
declare(strict_types=1);

namespace App\Core;

class QueryBuilder
{
    private string $table;
    private array $wheres = [];
    private array $bindings = [];
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $columns = ['*'];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function select(string|array $columns): self
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function where(string $column, mixed $operatorOrValue, mixed $value = null): self
    {
        if ($value === null) {
            $value = $operatorOrValue;
            $operator = '=';
        } else {
            $operator = $operatorOrValue;
        }
        $this->wheres[] = "{$column} {$operator} ?";
        $this->bindings[] = $value;
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        if (empty($values)) {
            $this->wheres[] = '1 = 0';
            return $this;
        }
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = "{$column} IN ({$placeholders})";
        array_push($this->bindings, ...$values);
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NULL";
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->wheres[] = "{$column} IS NOT NULL";
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = "{$column} {$direction}";
        return $this;
    }

    public function limit(int $n): self
    {
        $this->limit = $n;
        return $this;
    }

    public function offset(int $n): self
    {
        $this->offset = $n;
        return $this;
    }

    public function get(): array
    {
        $sql = 'SELECT ' . implode(', ', $this->columns) . " FROM {$this->table}";
        $sql .= $this->buildWhere();
        $sql .= $this->buildOrder();
        $sql .= $this->buildLimit();
        
        // Pastikan mengembalikan array
        return DB::raw($sql, $this->bindings);
    }

    public function first(): ?array
    {
        $this->limit(1);
        $rows = $this->get();
        return $rows[0] ?? null;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) AS c FROM {$this->table}" . $this->buildWhere();
        $row = DB::rawOne($sql, $this->bindings);
        return (int) ($row['c'] ?? 0);
    }

    public function insert(array $data): int
    {
        $cols = array_keys($data);
        $placeholders = implode(',', array_fill(0, count($cols), '?'));
        $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES ({$placeholders})";
        DB::rawExec($sql, array_values($data));
        return DB::lastInsertId();
    }

    public function update(array $data): int
    {
        $sets = [];
        $binds = [];
        foreach ($data as $col => $val) {
            $sets[] = "{$col} = ?";
            $binds[] = $val;
        }
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . $this->buildWhere();
        return DB::rawExec($sql, array_merge($binds, $this->bindings));
    }

    public function delete(): int
    {
        $sql = "DELETE FROM {$this->table}" . $this->buildWhere();
        return DB::rawExec($sql, $this->bindings);
    }

    private function buildWhere(): string
    {
        return empty($this->wheres) ? '' : ' WHERE ' . implode(' AND ', $this->wheres);
    }

    private function buildOrder(): string
    {
        return empty($this->orders) ? '' : ' ORDER BY ' . implode(', ', $this->orders);
    }

    private function buildLimit(): string
    {
        if ($this->limit === null) return '';
        $s = " LIMIT {$this->limit}";
        if ($this->offset !== null) $s .= " OFFSET {$this->offset}";
        return $s;
    }
    public function exists(): bool
    {
        $sql = "SELECT 1 FROM {$this->table}" . $this->buildWhere() . " LIMIT 1";
        $stmt = DB::pdo()->prepare($sql);
        $stmt->execute($this->bindings);
        return (bool) $stmt->fetchColumn();
    }

    public function paginate(int $perPage = 10, string $pageName = 'page'): array
    {
        $currentPage = max(1, (int)($_GET[$pageName] ?? 1));
        
        // Hitung total data
        $total = $this->count();
        
        // Hitung limit & offset
        $this->limit($perPage);
        $this->offset(($currentPage - 1) * $perPage);
        
        $data = $this->get();
        $lastPage = (int) ceil($total / $perPage);

        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $currentPage,
            'last_page' => $lastPage,
            'from' => $total > 0 ? ($currentPage - 1) * $perPage + 1 : 0,
            'to' => min($currentPage * $perPage, $total),
        ];
    }
}