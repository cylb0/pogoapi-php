<?php

final class Fixtures {

    public function usersFixtures(): array {
        return [
            [
                "username" => "test1",
                "password" => "Password123!",
                "email" => "email@example.com"
            ],
            [
                "username" => "test2",
                "password" => "Password123?",
                "email" => "mail@test.com"
            ]
        ];
    }

}