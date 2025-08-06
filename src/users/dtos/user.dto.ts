import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import {
	IsBoolean,
	IsDateString,
	IsEmail,
	IsEnum,
	IsOptional,
	IsString,
	IsUUID,
	MaxLength,
	MinLength,
} from 'class-validator';
import { User } from '@/users/types/user';
import { UserRole } from '@/users/enums/user-role';

export class UserDto implements User {
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
	 * Bio of the user
	 */
	@ApiPropertyOptional()
	@IsOptional()
	@IsString()
	@MinLength(1)
	bio: string | null;

	/**
	 * Role of the user
	 */
	@ApiProperty({ enum: UserRole, default: UserRole.STANDARD_USER })
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
}
