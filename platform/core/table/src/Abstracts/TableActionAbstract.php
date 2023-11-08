<?php

namespace Botble\Table\Abstracts;

use Botble\Base\Contracts\BaseModel;
use Botble\Table\Abstracts\Concerns\HasConfirmation;
use Botble\Table\Abstracts\Concerns\HasLabel;
use Botble\Table\Abstracts\Concerns\HasPermissions;
use Botble\Table\Abstracts\Concerns\HasPriority;
use Botble\Table\Abstracts\Concerns\Renderable;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

abstract class TableActionAbstract implements Htmlable, Stringable
{
    use Conditionable;
    use HasConfirmation;
    use HasLabel;
    use HasPermissions;
    use HasPriority;
    use Renderable;

    protected BaseModel $model;

    public function __construct(protected string $name)
    {
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function model(BaseModel $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): BaseModel
    {
        return $this->model;
    }

    public function toHtml(): string
    {
        return $this->render();
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
