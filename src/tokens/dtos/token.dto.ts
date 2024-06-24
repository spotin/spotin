import { Token } from '@/tokens/types/token';
import { ApiProperty } from '@nestjs/swagger';
import {
	IsDateString,
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
	 * Name of the token
	 */
	@IsString()
	@MinLength(1)
	@MaxLength(255)
	name: string;

	/**
	 * Hash of the token
	 */
	@ApiPropertyOptional()
	@IsString()
	@IsOptional()
	@MinLength(1)
	@MaxLength(255)
	hash?: string;

	/**
	 * Date when the token was created
	 */
	@IsDateString()
	createdAt: Date;

	/**
	 * Date when the token was updated
	 */
	@IsDateString()
	updatedAt: Date;
}
