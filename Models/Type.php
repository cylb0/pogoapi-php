<?php

final class Type {

    private $id;
    private $name_en;

    public function __construct(int $id, string $name_en) {
        $this->id = $id;
        $this->name_en = $name_en;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNameEn(): string {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): void {
        $this->name_en = $name_en;
    }

}