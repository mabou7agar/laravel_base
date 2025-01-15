<?php

namespace  BasePackage\Shared\Repositories;

interface Repository
{
    public function create(array $attributes);

    public function update($id, array $data);

    public function delete($id);

    public function find($id);

    public function list(array $conditions = [], string $orderBy = 'id', string $sortBy = 'asc');

    public function paginatedList(
        array $conditions = [],
        int $page = 1,
        int $perPage = 15,
        string $orderBy = 'id',
        string $sortBy = 'asc'
    );

    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc');

    public function findOneOrFail($id);

    public function findBy(array $data);

    public function updateBy(array $findList, array $dataList = []);

    public function countBy(array $data);

    public function findByWithRelations(array $data, array $relations = []);

    public function findOneBy(array $data);

    public function findOneByWithRelations(array $data, array $relations = []);

    public function findOneByOrFail(array $data);

    public function findOneByWithRelationsOrFail(array $data, array $relations = []);
}
