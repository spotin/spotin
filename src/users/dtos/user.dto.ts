import { ApiProperty } from '@nestjs/swagger';
import { User, UserRole } from '@prisma/client';
import {
	IsArray,
	IsBoolean,
	IsDateString,
	IsEmail,
	IsEnum,
	IsString,
	IsUUID,
	MaxLength,
	MinLength,
	ValidateNested,
} from 'class-validator';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { Type } from 'class-transformer';

export class UserDto implements Omit<User, 'resetPasswordRequestId'> {
	/**
	 * Identification of the user
	 */
	@ApiProperty({ format: 'uuid' })
	@IsUUID()
	id: string;

	/**
	 * Username of the user
	 */
	@IsString()
	@MinLength(3)
	@MaxLength(255)
	username: string;

	/**
	 * Email of the user
	 */
	@ApiProperty({ format: 'email' })
	@IsEmail()
	@MaxLength(255)
	email: string;

	/**
	 * Password of the user
	 */
	@ApiProperty({ format: 'password' })
	@IsString()
	@MinLength(8)
	@MaxLength(255)
	password: string;

	/**
	 * Role of the user
	 */
	@ApiProperty({ enum: UserRole })
	@IsEnum(UserRole)
	role: UserRole;

	/**
	 * Set if the user is enabled (and have access to the platform)
	 */
	@ApiProperty({ default: false })
	@IsBoolean()
	enabled = false;

	/**
	 * Date when the user was created
	 */
	@IsDateString()
	createdAt: Date;

	/**
	 * Date when the user was updated
	 */
	@IsDateString()
	updatedAt: Date;

	/**
	 * Date when the spot was deleted
	 */
	@IsDateString()
	deletedAt: Date | null;

	/**
	 * Spots created by the user
	 */
	@IsArray()
	@ValidateNested({ each: true })
	@Type(() => ReadSpotDto)
	spots: ReadSpotDto[];
}
