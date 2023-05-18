import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';
import { Token } from '@prisma/client';
import { IsDateString, IsOptional, IsString, IsUUID } from 'class-validator';

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
  hash: string;

  /**
   * Name of the token
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsString()
  name: string | null;

  /**
   * User who owns the token
   */
  @IsString()
  userId: string;

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

  /**
   * Date when the token was deleted
   */
  @ApiPropertyOptional()
  @IsOptional()
  @IsDateString()
  deletedAt: Date | null;
}
