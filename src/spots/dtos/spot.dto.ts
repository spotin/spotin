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
  IsUrl,
  MaxLength,
  MinLength,
} from 'class-validator';
import { Spot } from '@prisma/client';

export class SpotDto implements Spot {
  /**
   * Identification of the spot
   */
  @ApiProperty({ format: 'uuid' })
  @IsUUID()
  id: string;

  /**
   * Title of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  @MinLength(1)
  @MaxLength(255)
  title: string | null;

  /**
   * Description of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  @MinLength(1)
  description: string | null;

  /**
   * Latitude of the position of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsNumber()
  @Min(-90)
  @Max(90)
  latitude: number | null;

  /**
   * Longitude of the position of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsNumber()
  @Min(-180)
  @Max(180)
  longitude: number | null;

  /**
   * Redirection when the QR code is accessed
   */
  @ApiPropertyOptional({ format: 'url' })
  @IsOptional()
  @IsUrl()
  @MaxLength(255)
  redirection: string | null;

  /**
   * User who created the spot
   */
  @IsString()
  userId: string;

  /**
   * Set if the spot will be referenced on the website
   */
  // eslint-disable-next-line @typescript-eslint/no-inferrable-types
  @ApiPropertyOptional({ default: false })
  @IsOptional()
  @IsBoolean()
  referenced: boolean = false;

  /**
   * Set if the spot is already configured or not
   */
  // eslint-disable-next-line @typescript-eslint/no-inferrable-types
  @ApiPropertyOptional({ default: true })
  @IsOptional()
  @IsBoolean()
  configured: boolean = true;

  /**
   * Date when the spot was created
   */
  @IsDateString()
  createdAt: Date;

  /**
   * Date when the spot was updated
   */
  @IsDateString()
  updatedAt: Date;

  /**
   * Date when the spot was deleted
   */
  @IsDateString()
  @ApiPropertyOptional()
  @IsOptional()
  deletedAt: Date | null;
}
