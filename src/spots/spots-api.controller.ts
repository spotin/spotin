import {
  BadRequestException,
  Body,
  Controller,
  Get,
  Param,
  Res,
} from '@nestjs/common';
import {
  ApiOperation,
  ApiOkResponse,
  ApiTags,
  ApiParam,
  ApiNotFoundResponse,
} from '@nestjs/swagger';
import { Response } from 'express';
import { Spot, User, UserRole } from '@prisma/client';
import { CreateSpotDto } from '@/spots/dtos/create-spot.dto';
import { UpdateSpotDto } from '@/spots/dtos/update-spot-type.dto';
import { SpotsService } from '@/spots/spots.service';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { CustomPost } from '@/common/decorators/custom-post.decorator';
import { GetOne } from '@/common/decorators/get-one.decorator';
import { GetMany } from '@/common/decorators/get-many.decorator';
import { CustomPatch } from '@/common/decorators/custom-patch.decorator';
import { CustomDelete } from '@/common/decorators/custom-delete.decorator';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { TokenOrJwtAuth } from '@/auth/token-or-jwt/token-or-jwt-auth.decorators';
import { JwtOrTokenOrUnconfiguredSpotAuth } from '@/auth/jwt-or-token-or-unconfigured-spot/jwt-or-token-or-unconfigured-spot-auth.decorators';

@ApiTags('Spots')
@Controller('api/spots')
export class SpotsApiController {
  constructor(private readonly spotsService: SpotsService) {}

  @GetMany({
    path: 'public',
    name: 'Spots',
    summary: 'Get the public spots',
    operationId: 'getPublicSpotsApi',
    responseType: [ReadSpotDto],
  })
  async getPublicSpotsApi() {
    const spots = await this.spotsService.getPublicSpots();

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @GetMany({
    name: 'Spots',
    summary: 'Get the spots',
    operationId: 'getSpotsApi',
    responseType: [ReadSpotDto],
  })
  @TokenOrJwtAuth()
  async getSpotsApi(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return spotsDto;
  }

  @GetOne({
    name: 'Spot',
    summary: 'Get the specified spot',
    operationId: 'getSpotApi',
    responseType: ReadSpotDto,
  })
  async getSpotApi(@Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);

    return new ReadSpotDto(spot);
  }

  @CustomPost({
    name: 'Spot',
    summary: 'Create a new spot',
    bodyType: CreateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'createSpotApi',
  })
  @TokenOrJwtAuth()
  async createSpotApi(
    @AuthUser() user: User,
    @Body() createSpotDto: CreateSpotDto,
  ) {
    const newSpot = await this.spotsService.createSpot(createSpotDto, user);

    return new ReadSpotDto(newSpot);
  }

  @CustomPatch({
    name: 'Spot',
    summary: 'Update the specified spot',
    bodyType: UpdateSpotDto,
    responseType: ReadSpotDto,
    operationId: 'updateSpotApi',
  })
  @JwtOrTokenOrUnconfiguredSpotAuth()
  async updateSpotApi(
    @AuthUser() user: User,
    @Param('id') id: string,
    @Body() updateSpot: UpdateSpotDto,
  ) {
    if (user.role === UserRole.GUEST) {
      updateSpot.configured = true;
      updateSpot.referenced = undefined;

      if (!updateSpot.redirection || updateSpot.redirection === '') {
        throw new BadRequestException();
      }
    }

    const updatedSpot = await this.spotsService.updateSpot(
      id,
      updateSpot,
      user,
    );

    return new ReadSpotDto(updatedSpot);
  }

  @CustomDelete({
    name: 'Spot',
    summary: 'Delete the specified spot',
    operationId: 'deleteSpotApi',
  })
  @TokenOrJwtAuth()
  async deleteSpotApi(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);
  }
}
