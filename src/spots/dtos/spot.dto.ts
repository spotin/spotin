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
  IsJSON,
} from 'class-validator';
import { Prisma, Spot } from '@prisma/client';
import { Transform, Type } from 'class-transformer';

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
  @Transform(({ value }) => (value ? value : null))
  title: string | null;

  /**
   * Description of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  @MinLength(1)
  @Transform(({ value }) => (value ? value : null))
  description: string | null;

  /**
   * Latitude of the position of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsNumber()
  @Min(-90)
  @Max(90)
  @Type(() => Number)
  @Transform(({ value }) => (value ? value : null))
  latitude: number | null;

  /**
   * Longitude of the position of the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsNumber()
  @Min(-180)
  @Max(180)
  @Type(() => Number)
  @Transform(({ value }) => (value ? value : null))
  longitude: number | null;

  /**
   * Payload to store inconsistent data about the spot
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsJSON()
  @MaxLength(510)
  @Transform(({ value }) => (value ? value : null))
  payload: Prisma.JsonValue | null;

  /**
   * Redirection when the QR code is accessed
   */
  @ApiPropertyOptional({ format: 'url' })
  @IsOptional()
  @IsUrl()
  @MaxLength(255)
  @Transform(({ value }) => (value ? value : null))
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
  @Type(() => Boolean)
  referenced: boolean = false;

  /**
   * Set if the spot is already configured or not
   */
  // eslint-disable-next-line @typescript-eslint/no-inferrable-types
  @ApiPropertyOptional({ default: true })
  @IsOptional()
  @IsBoolean()
  @Type(() => Boolean)
  configured: boolean = true;

  /**
   * Date when the spot was created
   */
  @IsDateString()
  @Type(() => Date)
  createdAt: Date;

  /**
   * Date when the spot was updated
   */
  @IsDateString()
  @Type(() => Date)
  updatedAt: Date;

  /**
   * Date when the spot was deleted
   */
  @IsDateString()
  @ApiPropertyOptional()
  @IsOptional()
  @Type(() => Date)
  deletedAt: Date | null;
}
