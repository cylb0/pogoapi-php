<?php

/**
 * Represents a Pokemon type.
 * 
 * @property-read int $id The ID of the type.
 * @property string $name_en The english name for the type.
 * @property string $name_fr The french name for the type.
 * @property Type[] $strong_against The collection of types this type is effective against.
 * @property Type[] $vulnerable_to The collection of types that are effective against this type.
 * @property Type[] $weak_against The collection of types this type is not effective against.
 * @property Type[] $resistant_to The collection of types that are not effective against this type.
 */
final class Type {

    private $id;
    private $name_en;
    private $name_fr;
    private $strong_against = [];
    private $vulnerable_to = [];
    private $weak_against = [];
    private $resistant_to = [];

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

    public function getStrongAgainst(): array {
        return $this->strong_against;
    }

    public function addStrongAgainst(Type $type): void {
        if (!in_array($type, $this->strong_against, true)) {
            $this->strong_against[] = $type;
        }
    }

    public function removeStrongAgainst(Type $type): void {
        $index = array_search($type, $this->strong_against, true);
        if ($index !== false) {
            array_splice($this->strong_against, $index, 1);
        }
    }

    public function getVulnerableTo(): array {
        return $this->vulnerable_to;
    }

    public function addVulnerableTo(Type $type): void {
        if (!in_array($type, $this->vulnerable_to, true)) {
            $this->vulnerable_to[] = $type;
        }
    }

    public function removeVulnerableTo(Type $type): void {
        $index = array_search($type, $this->vulnerable_to, true);
        if ($index !== false) {
            array_splice($this->vulnerable_to, $index, 1);
        }
    }

    public function getWeakAgainst(): array {
        return $this->weak_against;
    }

    public function addWeakAgainst(Type $type): void {
        if (!in_array($type, $this->weak_against, true)) {
            $this->weak_against[] = $type;
        }
    }
    
    public function removeWeakAgainst(Type $type): void {
        $index = array_search($type, $this->weak_against, true);
        if ($index !== false) {
            array_splice($this->weak_against, $index, 1);
        }
    }

    public function getResistantTo(): array {
        return $this->resistant_to;
    }

    public function addResistantTo(Type $type): void {
        if (!in_array($type, $this->resistant_to, true)) {
            $this->resistant_to[] = $type;
        }
    }

    public function removeResistantTo(Type $type): void {
        $index = array_search($type, $this->resistant_to, true);
        if ($index !== false) {
            array_splice($this->resistant_to, $index, 1);
        }
    }

}