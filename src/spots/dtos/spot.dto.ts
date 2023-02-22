import { ApiProperty } from '@nestjs/swagger';

export class Spot {
  @ApiProperty({
    description: 'Identification of the spot',
    required: true,
    type: String,
  })
  uuid: string;

  @ApiProperty({
    description: 'Title of the spot',
    required: false,
    type: String,
  })
  title?: string;

  @ApiProperty({
    description: 'Description of the spot',
    required: false,
    type: String,
  })
  description?: string;

  @ApiProperty({
    description: 'Latitude of the position of the spot',
    required: false,
    type: Number,
  })
  latitude?: number;

  @ApiProperty({
    description: 'Longitude of the position of the spot',
    required: false,
    type: Number,
  })
  longitude?: number;

  @ApiProperty({
    description: 'TODO',
    required: false,
    type: Date,
  })
  timestamp?: string | Date | null;

  @ApiProperty({
    description: 'Redirection when the QR code is accessed',
    required: false,
    type: String,
  })
  redirection?: string;

  @ApiProperty({
    description: 'Set if the spot will be referenced on the website',
    required: false,
    type: Boolean,
  })
  referenced?: boolean;

  @ApiProperty({
    description: 'Date when the spot was created',
    required: true,
    type: Number,
  })
  created_at: number;

  @ApiProperty({
    description: 'Date when the spot was updated',
    required: true,
    type: Number,
  })
  updated_at: number;

  @ApiProperty({
    description: 'Date when the spot was deleted',
    required: false,
    type: Number,
  })
  deleted_at?: number;
}
