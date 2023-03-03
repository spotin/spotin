import { ApiProperty, getSchemaPath } from '@nestjs/swagger';
import { Spot } from '@prisma/client';

export class SpotDto implements Spot {
  @ApiProperty({
    description: 'Identification of the spot',
    required: true,
    type: String,
    format: 'uuid',
  })
  id: string;

  @ApiProperty({
    description: 'Title of the spot',
    required: false,
    type: String,
  })
  title: string | null;

  @ApiProperty({
    description: 'Description of the spot',
    required: false,
    type: String,
  })
  description: string | null;

  @ApiProperty({
    description: 'Latitude of the position of the spot',
    required: false,
    type: Number,
  })
  latitude: number | null;

  @ApiProperty({
    description: 'Longitude of the position of the spot',
    required: false,
    type: Number,
  })
  longitude: number | null;

  @ApiProperty({
    description: 'TODO',
    required: false,
    type: Date,
  })
  timestamp: Date | null;

  @ApiProperty({
    description: 'Redirection when the QR code is accessed',
    required: false,
    type: String,
  })
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

  @ApiProperty({
    description: 'Set if the spot will be referenced on the website',
    required: false,
    type: Boolean,
  })
  referenced: boolean;

  @ApiProperty({
    description: 'Set if the spot is already configured or not',
    required: true,
    type: Boolean,
  })
  configured: boolean;

  @ApiProperty({
    description: 'Date when the spot was created',
    required: true,
    type: Number,
  })
  createdAt: Date;

  @ApiProperty({
    description: 'Date when the spot was updated',
    required: true,
    type: Number,
  })
  updatedAt: Date;

  @ApiProperty({
    description: 'Date when the spot was deleted',
    required: false,
    type: Number,
  })
  deletedAt: Date | null;
}
