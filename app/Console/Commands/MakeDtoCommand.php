<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\GeneratorCommand;

#[Signature('make:dto {name : The name of the DTO class}')]
#[Description('Create a new Data Transfer Object class')]
class MakeDtoCommand extends GeneratorCommand
{
    /**
     * Sınıf tipini belirler (Hata mesajlarında gösterilir).
     */
    protected $type = 'DTO';

    /**
     * Şablon (Stub) dosyasının konumunu belirtir.
     */
    protected function getStub(): string
    {
        return base_path('stubs/dto.stub');
    }

    /**
     * DTO'ların varsayılan olarak hangi klasörde oluşturulacağını belirler.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\DTOs';
    }
}
