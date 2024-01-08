<?php

final class Type {

    private $id;
    private $name_en;
    private $name_fr;

    public function __construct(int $id, string $name_en, string $name_fr = null) {
        $this->id = $id;
        $this->name_en = $name_en;
        $this->name_fr = $name_fr;
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

    public function getNameFr(): string {
        return $this->name_fr;
    }

    public function setNameFr(string $name_fr): void {
        $this->name_fr = $name_fr;
    }

}