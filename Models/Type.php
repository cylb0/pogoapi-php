<?php

final class Type {

    private $id;
    private $name_en;

    public function __construct(int $id, string $name_en) {
        $this->id = $id;
        $this->name_en = $name_en;
    }

    private function getId(): int {
        return $this->id;
    }

    private function setId(int $id): void {
        $this->id = $id;
    }

    private function getNameEn(): string {
        return $this->name_en;
    }

    private function setNameEn(string $name_en): void {
        $this->name_en = $name_en;
    }

}