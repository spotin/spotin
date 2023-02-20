import { Injectable } from '@nestjs/common';
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
    return await this.prisma.$queryRawUnsafe<Spot>(
      'INSERT INTO spots(title, description, redirection, uuid) VALUES ($1, $2, $3, $4::uuid);',
      createSpot.title,
      createSpot.description,
      createSpot.redirection,
      randomUUID(),
    );
  }
}
