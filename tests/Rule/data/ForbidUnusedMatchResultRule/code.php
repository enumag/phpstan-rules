<?php declare(strict_types = 1);

namespace ForbidUnusedMatchResultRule;


use Exception;
use LogicException;
use RuntimeException;

class Foo {}
class Bar {}


class Clazz {

    public function testUsed(bool $bool): mixed
    {
        match (true) {
            default => $this->voidMethod(),
        };

        match ($bool) {
            false => "Foo",
            true => "Bar",
        } ?? null;

        $this->use(match ($bool) {
            false => new LogicException(),
            true => new RuntimeException(),
        });

        yield match ($bool) {
            false => new LogicException(),
            true => new RuntimeException(),
        };

        try {
            match ($bool) {
                false => throw new LogicException(),
                true => throw new RuntimeException(),
            };
        } catch (\Throwable $e) {}

        return match ($bool) {
            false => 1,
            true => 2,
        };
    }

    public function testUnused(object $class, bool $bool, int $int): void
    {
        match (true) { // error: Unused match result detected, possible returns: null
            $class instanceof Foo => $this->voidMethod(),
            default => null,
        };

        match ($bool) { // error: Unused match result detected, possible returns: int
            false => 0,
            true => 1,
        };

        match ($int) { // error: Unused match result detected, possible returns: Exception
            0 => new LogicException(),
            1 => new RuntimeException(),
            default => new Exception(),
        };
    }

    public function voidMethod(): void {}
    private function use(mixed $param): void {}
}


