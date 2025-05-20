<?php

namespace App\Http\Common\Utils;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class Filtering
{
    private ?string $order;
    private ?string $query;
    private ?string $status;
    private ?bool $offLimit;
    private Builder $builder;
    private string $orderField;
    private string $searchField;

    public function __construct(Request $request)
    {
        $this->query = $request->search ?? null;
        $this->status = $request->status ?? null;
        $this->order = $request->orderBy === 'true' ? 'desc' : 'asc';
        $this->offLimit = $request->offLimit;
    }

    public function setBuilder(Builder $builder, string $searchField = 'name', string $orderField = 'id'): static
    {
        $this->builder = $builder;
        $this->searchField = $searchField;
        $this->orderField = $orderField;

        return $this;
    }

    public function apply(): LengthAwarePaginator | Collection
    {
        if ($this->query) {
            $this->builder->where($this->searchField, 'like', '%' . $this->query . '%');
        }

        if ($this->status) {
            $this->builder->where('status', $this->status);
        }

        $this->builder->orderBy($this->orderField, $this->order);

        return $this->offLimit
            ? $this->builder->get()
            : $this->builder->paginate(5);
    }
}
