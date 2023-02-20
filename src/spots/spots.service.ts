import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
import { randomUUID } from 'crypto';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateSpot } from './types/create-spot-type';
import { Spot } from './types/spot.type';
import { UpdateSpot } from './types/update-spot-type';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  async getSpots() {
    return await this.prisma.spots.findMany();
  }

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

  async createSpot(createSpot: CreateSpot) {
    return await this.prisma.$queryRaw<Spot[]>(
      Prisma.sql`INSERT INTO spots(uuid,title,description,coordinates,timestamp,redirection,referenced) VALUES (
      ${randomUUID()}::uuid,
      ${createSpot.title},
      ${createSpot.description},
      ST_MakePoint(${createSpot.longitude},${createSpot.latitude})::geometry,
      ${createSpot.timestamp}::timestamp,
      ${createSpot.redirect},
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

  async updateSpot(spotId: string, updateSpot: UpdateSpot) {
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
      redirection = ${updateSpot.redirect ?? spot.redirect},
      referenced = ${updateSpot.referenced ?? spot.referenced},
      created_at = ${updateSpot.created_at ?? spot.created_at},
      updated_at = ${updateSpot.updated_at ?? spot.updated_at},
      deleted_at = ${updateSpot.deleted_at ?? spot.deleted_at}
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
