<?php

final class Fixtures {

    public function usersFixtures(): array {
        return [
            [
                'username' => 'test1',
                'password' => '$2y$10$ZkGUrwh8E3NrF/jYYnPmdeexfdA/a.cmaL5x4iX/mZZGBJhkldW.O',
                'email' => 'email@example.com'
            ],
            [
                'username' => 'test2',
                'password' => '$2y$10$UhH4IbwqeWZw0xl66nzO.uQH/LOf7MBs/Xfr/SaJcFcQK45zF1rGy',
                'email' => 'mail@test.com'
            ]
        ];
    }
    
    public function typesFixtures(): array {
        return [
            [
                'name_en' => 'water',
            ],
            [
                'name_en' => 'grass',
            ],
            [
                'name_en' => 'fire',
            ],
        ];
    }

}