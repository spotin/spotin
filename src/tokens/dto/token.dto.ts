import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { Token } from '@prisma/client';
import { Type } from 'class-transformer';
import {
	IsDateString,
	IsOptional,
	IsString,
	IsUUID,
	MaxLength,
	MinLength,
} from 'class-validator';

export class TokenDto implements Token {
	/**
	 * Identification of the token
	 */
	@ApiProperty({ format: 'uuid' })
	@IsUUID()
	id: string;

	/**
	 * Hash of the token
	 */
	@IsString()
	@MinLength(1)
	@MaxLength(255)
	hash: string;

	/**
	 * Name of the token
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsString()
	@MinLength(1)
	@MaxLength(255)
	name: string;

	/**
	 * User who owns the token
	 */
	@IsString()
	userId: string;

	/**
	 * Date when the token was created
	 */
	@IsDateString()
	@Type(() => Date)
	createdAt: Date;

	/**
	 * Date when the token was updated
	 */
	@IsDateString()
	@Type(() => Date)
	updatedAt: Date;

	/**
	 * Date when the token was deleted
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsDateString()
	@Type(() => Date)
	deletedAt: Date | null;
}
