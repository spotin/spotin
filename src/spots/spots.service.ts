import { Injectable } from '@nestjs/common';
import { Prisma } from '@prisma/client';
import { randomUUID } from 'crypto';
import { PrismaService } from 'src/prisma/prisma.service';
import { CreateSpot } from './types/create-spot-type';
import { Spot } from './types/spot.type';

@Injectable()
export class SpotsService {
  constructor(private readonly prisma: PrismaService) {}

  async getSpots() {
    return await this.prisma.spots.findMany();
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
      ST_AsText(coordinates) as coordinates,
      redirection,
      referenced,
      created_at,
      updated_at,
      deleted_at`,
    );
  }
}
