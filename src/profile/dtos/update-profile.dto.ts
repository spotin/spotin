import {
	ApiProperty,
	ApiPropertyOptional,
	OmitType,
	PartialType,
} from '@nestjs/swagger';
import { IsString, MaxLength, MinLength, IsOptional } from 'class-validator';
import { UpdateProfile } from '@/profile/types/update-profile';
import { ProfileDto } from '@/profile/dtos/profile.dto';

export class UpdateProfileDto
	extends PartialType(OmitType(ProfileDto, ['email', 'createdAt'] as const))
	implements UpdateProfile
{
	/**
	 * Current password for the user
	 */
	@ApiProperty({ format: 'password' })
	@IsString()
	@MinLength(8)
	@MaxLength(255)
	currentPassword: string;

	/**
	 * New password for the user
	 */
	@ApiPropertyOptional({ format: 'password' })
	@IsOptional()
	@IsString()
	@MinLength(8)
	@MaxLength(255)
	newPassword?: string;
}
