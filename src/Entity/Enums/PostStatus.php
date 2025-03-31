<?php

namespace App\Entity\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case IN_REVIEW = 'in_review';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
