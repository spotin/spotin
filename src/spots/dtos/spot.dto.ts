import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import {
  IsBoolean,
  IsDateString,
  IsNumber,
  IsString,
  IsUUID,
  Min,
  Max,
  IsOptional,
} from 'class-validator';
import { Spot } from '@prisma/client';

export class SpotDto implements Spot {
  // Identification of the spot
  @ApiProperty({ format: 'uuid' })
  @IsUUID()
  id: string;

  // Title of the spot
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  title: string | null;

  // Description of the spot
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  description: string | null;

  // Latitude of the position of the spot
  @ApiPropertyOptional()
  @IsOptional()
  @IsNumber()
  @Min(-90)
  @Max(90)
  latitude: number | null;

  // Longitude of the position of the spot
  @ApiPropertyOptional()
  @IsOptional()
  @IsNumber()
  @Min(-180)
  @Max(180)
  longitude: number | null;

  // Redirection when the QR code is accessed
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  redirection: string | null;

  // User who created the spot
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  userId: string | null;

  // Set if the spot will be referenced on the website
  @ApiPropertyOptional({ default: false })
  @IsOptional()
  @IsBoolean()
  referenced: boolean;

  // Set if the spot is already configured or not
  @ApiPropertyOptional({ default: true })
  @IsOptional()
  @IsBoolean()
  configured: boolean;

  // Date when the spot was created
  @IsDateString()
  createdAt: Date;

  // Date when the spot was updated
  @IsDateString()
  updatedAt: Date;

  // Date when the spot was deleted
  @IsDateString()
  deletedAt: Date | null;
}
