import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
import { randomUUID } from 'crypto';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateSpotDto } from './dtos/create-spot.dto';
import { Spot } from './dtos/spot.dto';
import { UpdateSpotDto } from './dtos/update-spot-type.dto';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  /** List spots */
  async getSpots() {
    return await this.prisma.spots.findMany();
  }

  /** Read a spot by id */
  async getSpot(spotId: string) {
    return await this.prisma.$queryRaw<Spot[]>(
      Prisma.sql`SELECT uuid,
      title,
      description,
      timestamp,
      ST_X (ST_Transform (coordinates, 4326)) as longitude,
      ST_Y (ST_Transform (coordinates, 4326)) as latitude,
      redirection,
      referenced,
      created_at,
      updated_at,
      deleted_at FROM spots WHERE uuid = ${spotId}::uuid`,
    );
  }

  /** Create a new spot */
  async createSpot(createSpot: CreateSpotDto) {
    return await this.prisma.$queryRaw<Spot[]>(
      Prisma.sql`INSERT INTO spots(uuid,title,description,coordinates,timestamp,redirection,referenced) VALUES (
      ${randomUUID()}::uuid,
      ${createSpot.title},
      ${createSpot.description},
      ST_MakePoint(${createSpot.longitude},${createSpot.latitude})::geometry,
      ${createSpot.timestamp}::timestamp,
      ${createSpot.redirection},
      ${createSpot.referenced}) 
      RETURNING uuid,
      title,
      description,
      timestamp,
      ST_X (ST_Transform (coordinates, 4326)) as longitude,
      ST_Y (ST_Transform (coordinates, 4326)) as latitude,
      redirection,
      referenced,
      created_at,
      updated_at,
      deleted_at`,
    );
  }

  /** Update a spot by id */
  async updateSpot(spotId: string, updateSpot: UpdateSpotDto) {
    const [spot] = await this.getSpot(spotId);

    return await this.prisma.$queryRaw<Spot[]>(
      Prisma.sql`UPDATE spots SET
      title = ${updateSpot.title ?? spot.title},
      description = ${updateSpot.description ?? spot.description},
      timestamp = ${updateSpot.timestamp ?? spot.timestamp}::timestamp,
      coordinates = ST_MakePoint(
        ${updateSpot.longitude},
        ${updateSpot.latitude}
        )::geometry,
      redirection = ${updateSpot.redirection ?? spot.redirection},
      referenced = ${updateSpot.referenced ?? spot.referenced},
      updated_at = CURRENT_TIMESTAMP
      WHERE uuid = ${spotId}::uuid
      RETURNING uuid,
      title,
      description,
      timestamp,
      ST_X (ST_Transform (coordinates, 4326)) as longitude,
      ST_Y (ST_Transform (coordinates, 4326)) as latitude,
      redirection,
      referenced,
      created_at,
      updated_at,
      deleted_at`,
    );
  }

  /** Delete a spot by id */
  async deleteSpot(spotId: string) {
    return await this.prisma.$queryRaw<Spot[]>(
      Prisma.sql`DELETE FROM spots WHERE uuid = ${spotId}::uuid
      RETURNING uuid,
      title,
      description,
      timestamp,
      ST_X (ST_Transform (coordinates, 4326)) as longitude,
      ST_Y (ST_Transform (coordinates, 4326)) as latitude,
      redirection,
      referenced,
      created_at,
      updated_at,
      deleted_at`,
    );
  }
}
