import {
  ApiProperty,
  ApiPropertyOptional,
  getSchemaPath,
} from '@nestjs/swagger';
import {
  IsBoolean,
  IsDateString,
  IsNumber,
  IsString,
  IsUUID,
  Min,
  Max,
} from 'class-validator';
import { Spot } from '@prisma/client';

export class SpotDto implements Spot {
  // Identification of the spot
  @ApiProperty({ format: 'uuid' })
  @IsUUID()
  id: string;

  // Title of the spot
  @ApiPropertyOptional()
  @IsString()
  title: string | null;

  // Description of the spot
  @ApiPropertyOptional()
  @IsString()
  description: string | null;

  // Latitude of the position of the spot
  @ApiPropertyOptional()
  @IsNumber()
  @Min(-90)
  @Max(90)
  latitude: number | null;

  // Longitude of the position of the spot
  @ApiPropertyOptional()
  @IsNumber()
  @Min(-180)
  @Max(180)
  longitude: number | null;

  // TODO
  @ApiPropertyOptional()
  @IsDateString()
  timestamp: Date | null;

  // Redirection when the QR code is accessed
  @ApiPropertyOptional()
  @IsString()
  redirection: string | null;

  // @ApiProperty({
  //   oneOf: [
  //     // {
  //     //   $ref: getSchemaPath('ProjectSubsetDto'),
  //     // },
  //     {
  //       description: 'Id of the user linked to the Spot',
  //       type: 'string',
  //       format: 'uuid',
  //     },
  //   ],
  // })
  // userId: string | null;

  // Set if the spot will be referenced on the website
  @ApiPropertyOptional({ default: false })
  @IsBoolean()
  referenced: boolean;

  // Set if the spot is already configured or not
  @ApiPropertyOptional({ default: true })
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
