import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { Spot, User } from '@prisma/client';
import { IsDateString, IsOptional, IsString, IsUUID } from 'class-validator';

export class UserDto implements User {
  // Identification of the user
  @ApiProperty({ format: 'uuid' })
  @IsUUID()
  id: string;

  // Username of the user
  @IsString()
  username: string;

  // Email of the user
  @ApiPropertyOptional()
  @IsOptional()
  email: string;

  // Password of the user
  @ApiPropertyOptional()
  @IsOptional()
  password: string;

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
  @ApiPropertyOptional()
  @IsOptional()
  spots: Spot[];
}
