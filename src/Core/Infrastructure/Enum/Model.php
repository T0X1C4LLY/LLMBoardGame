<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Enum;

enum Model: string
{
    case CHAT_GPT_3_5_TURBO = 'gpt-3.5-turbo';
    case BARD = 'bard';
    case PHI_2 = 'phi';
    case PHI_3 = 'phi3';
    case LLAMA_2_13_B = 'llama2:13b';
    case LLAMA_3_8_B = 'llama3';
}
