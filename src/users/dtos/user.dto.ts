import { ApiProperty } from '@nestjs/swagger';
import { User } from '@prisma/client';
import {
  IsBoolean,
  IsDateString,
  IsEmail,
  IsString,
  IsUUID,
  MinLength,
} from 'class-validator';
import { ReadSpotDto } from 'src/spots/dtos/read-spot.dto';

export class UserDto implements User {
  // Identification of the user
  @ApiProperty({ format: 'uuid' })
  @IsUUID()
  id: string;

  // Username of the user
  @IsString()
  @MinLength(3)
  username: string;

  // Email of the user
  @ApiProperty({ format: 'email' })
  @IsEmail()
  email: string;

  // Password of the user
  @ApiProperty({ format: 'password' })
  @MinLength(8)
  @IsString()
  password: string;

  // Set if the user is enabled (and have access to the platform)
  @ApiProperty({ default: false })
  @IsBoolean()
  enabled = false;

  // Date when the spot was created
  @IsDateString()
  createdAt: Date;

  // Date when the spot was updated
  @IsDateString()
  updatedAt: Date;

  // Date when the spot was deleted
  @IsDateString()
  deletedAt: Date | null;

  // Spots created by the user
  spots: ReadSpotDto[];
}
